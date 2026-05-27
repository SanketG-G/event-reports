<?php

require_once __DIR__ . '/../core/BaseController.php';

class AiController extends BaseController
{
    public function __construct($pdo = null)
    {
        parent::__construct($pdo);
    }

    public function generateReport()
    {
        // Only accept POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        // Only allow authenticated users
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->json(['error' => 'Invalid JSON input'], 400);
        }

        $apiKey = $_ENV['OPENROUTER_API_KEY'] ?? getenv('OPENROUTER_API_KEY');
        if (empty($apiKey) || str_starts_with($apiKey, 'your_')) {
            return $this->json(['error' => 'OpenRouter API Key not configured in .env'], 500);
        }

        // Build the prompt from context
        $eventName = $input['eventName'] ?? 'an event';
        $participants = $input['participants'] ?? 'students';
        $topics = $input['topics'] ?? 'various topics';
        $activities = $input['activities'] ?? 'several activities';
        $guest = $input['guest'] ?? 'a guest speaker';
        $outcomes = $input['outcomes'] ?? 'positive outcomes';

        $prompt = "You are an assistant that writes formal event reports for a college.

Event Name: $eventName
Participants: $participants
Topics Covered: $topics
Activities Done: $activities
Guest Speaker: $guest
Outcomes: $outcomes

Generate a formal event report in JSON format ONLY. Do not include any markdown formatting like ```json or ```. Return a raw JSON object with exactly these keys:
\"description\"
\"activities\"
\"significance\"
\"conclusion\"
\"faculties_participation\"

Each section should contain a formal paragraph of 3-4 sentences based on the provided details. Use professional and academic language.";

        // Call OpenRouter API (OpenAI Compatible)
        // To switch to OpenAI later: change this URL to 'https://api.openai.com/v1/chat/completions'
        $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        // Using a free model available on OpenRouter
        // To switch to OpenAI later: change model to 'gpt-4o-mini'
        $payload = [
            "model" => "openai/gpt-oss-120b:free", 
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey,
            "HTTP-Referer: http://localhost", // Optional but recommended by OpenRouter
            "X-Title: Event Reports App", // Optional but recommended by OpenRouter
            "Content-Type: application/json"
        ]);

        // Add timeout since free tier might be slow
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("OpenRouter API Error: HTTP $httpCode - $response");
            return $this->json(['error' => 'Failed to generate content from AI. Try again later.'], 500);
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        // Clean the content in case the AI added markdown blocks like ```json ... ```
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```/', '', $content);
        $content = trim($content);

        $jsonResult = json_decode($content, true);

        if (!$jsonResult) {
            error_log("OpenRouter returned invalid JSON: $content");
            return $this->json(['error' => 'AI returned malformed data. Please try again.'], 500);
        }

        return $this->json($jsonResult);
    }

    /**
     * Generate AI content for Letter of Appreciation
     */
    public function generateAppreciation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->json(['error' => 'Invalid JSON input'], 400);
        }

        $apiKey = $_ENV['OPENROUTER_API_KEY'] ?? getenv('OPENROUTER_API_KEY');
        if (empty($apiKey) || str_starts_with($apiKey, 'your_')) {
            return $this->json(['error' => 'OpenRouter API Key not configured in .env'], 500);
        }

        $guestName    = $input['guestName'] ?? 'the guest';
        $designation  = $input['designation'] ?? '';
        $company      = $input['company'] ?? '';
        $eventName    = $input['eventName'] ?? 'an event';
        $eventDate    = $input['eventDate'] ?? '';
        $contribution = $input['contribution'] ?? 'valuable contribution';
        $collegeName  = $input['collegeName'] ?? 'our college';

        $prompt = "You are an assistant that writes formal letters of appreciation for a college.

Write a formal letter of appreciation body for:
Guest Name: $guestName
Guest Designation: $designation
Guest Company/Organization: $company
Event Name: $eventName
Event Date: $eventDate
Guest's Contribution: $contribution
College Name: $collegeName

Generate the letter body in JSON format ONLY. Do not include any markdown formatting like ```json or ```. Return a raw JSON object with exactly these keys:
\"subject\"
\"body\"

The \"subject\" should be a formal subject line like: \"Letter of Appreciation for [event name]\".

The \"body\" should be a formal, respectful appreciation letter body (3-4 paragraphs). It should:
- Thank the guest for their valuable time and contribution
- Mention the specific event and what they contributed
- Mention the positive impact on students and faculty
- Express desire for future collaboration
- Be written in professional, academic language
- Do NOT include date, recipient address, or signature — only the letter body text.";

        $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $payload = [
            "model" => "openai/gpt-oss-120b:free",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey,
            "HTTP-Referer: http://localhost",
            "X-Title: Event Reports App",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("OpenRouter API Error (Appreciation): HTTP $httpCode - $response");
            return $this->json(['error' => 'Failed to generate content from AI. Try again later.'], 500);
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```/', '', $content);
        $content = trim($content);

        $jsonResult = json_decode($content, true);

        if (!$jsonResult) {
            error_log("OpenRouter returned invalid JSON (Appreciation): $content");
            return $this->json(['error' => 'AI returned malformed data. Please try again.'], 500);
        }

        return $this->json($jsonResult);
    }

    /**
     * Generate AI content for Letter of Invitation
     */
    public function generateInvitation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->json(['error' => 'Invalid JSON input'], 400);
        }

        $apiKey = $_ENV['OPENROUTER_API_KEY'] ?? getenv('OPENROUTER_API_KEY');
        if (empty($apiKey) || str_starts_with($apiKey, 'your_')) {
            return $this->json(['error' => 'OpenRouter API Key not configured in .env'], 500);
        }

        $guestName    = $input['guestName'] ?? 'the guest';
        $designation  = $input['designation'] ?? '';
        $company      = $input['company'] ?? '';
        $eventName    = $input['eventName'] ?? 'an event';
        $eventDate    = $input['eventDate'] ?? '';
        $eventVenue   = $input['eventVenue'] ?? '';
        $eventTime    = $input['eventTime'] ?? '';
        $eventTopic   = $input['eventTopic'] ?? '';
        $guestRole    = $input['guestRole'] ?? 'chief guest';
        $collegeName  = $input['collegeName'] ?? 'our college';

        $prompt = "You are an assistant that writes formal letters of invitation for a college event.

Write a formal invitation letter body for:
Guest Name: $guestName
Guest Designation: $designation
Guest Company/Organization: $company
Event Name: $eventName
Event Date: $eventDate
Event Venue: $eventVenue
Event Time: $eventTime
Event Topic/Theme: $eventTopic
Guest's Role at Event: $guestRole
College Name: $collegeName

Generate the invitation letter in JSON format ONLY. Do not include any markdown formatting like ```json or ```. Return a raw JSON object with exactly these keys:
\"subject\"
\"body\"

The \"subject\" should be a formal subject line like: \"Invitation to [event name] as [role]\".

The \"body\" should be a formal, respectful invitation letter body (3-4 paragraphs). It should:
- Introduce the college and department organizing the event
- Clearly invite the guest to attend the event in the specified role
- Mention event details (name, date, venue, time, topic) naturally in the letter
- Express how their expertise and presence would benefit the students and faculty
- Request confirmation of their availability
- Be written in professional, academic language
- Do NOT include date, recipient address, or signature — only the letter body text.";

        $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $payload = [
            "model" => "openai/gpt-oss-120b:free",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey,
            "HTTP-Referer: http://localhost",
            "X-Title: Event Reports App",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("OpenRouter API Error (Invitation): HTTP $httpCode - $response");
            return $this->json(['error' => 'Failed to generate content from AI. Try again later.'], 500);
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```/', '', $content);
        $content = trim($content);

        $jsonResult = json_decode($content, true);

        if (!$jsonResult) {
            error_log("OpenRouter returned invalid JSON (Invitation): $content");
            return $this->json(['error' => 'AI returned malformed data. Please try again.'], 500);
        }

        return $this->json($jsonResult);
    }

    /**
     * Generate AI content for Notice
     */
    public function generateNotice()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->json(['error' => 'Invalid JSON input'], 400);
        }

        $apiKey = $_ENV['OPENROUTER_API_KEY'] ?? getenv('OPENROUTER_API_KEY');
        if (empty($apiKey) || str_starts_with($apiKey, 'your_')) {
            return $this->json(['error' => 'OpenRouter API Key not configured in .env'], 500);
        }

        $eventName    = $input['eventName'] ?? 'an event';
        $eventDate    = $input['eventDate'] ?? '';
        $eventVenue   = $input['eventVenue'] ?? '';
        $eventTime    = $input['eventTime'] ?? '';
        $eventTopic   = $input['eventTopic'] ?? '';
        $targetAudience = $input['targetAudience'] ?? 'all students and faculty';
        $guestName    = $input['guestName'] ?? '';
        $collegeName  = $input['collegeName'] ?? 'our college';
        $department   = $input['department'] ?? '';

        $prompt = "You are an assistant that writes formal event notices for a college.

Write a formal notice for:
Event Name: $eventName
Event Date: $eventDate
Event Venue: $eventVenue
Event Time: $eventTime
Event Topic/Theme: $eventTopic
Target Audience: $targetAudience
Guest Speaker(s): $guestName
Department: $department
College Name: $collegeName

Generate the notice content in JSON format ONLY. Do not include any markdown formatting like ```json or ```. Return a raw JSON object with exactly these keys:
\"dear\"
\"event_highlights\"

The \"dear\" field should be a formal opening paragraph (2-3 sentences) addressed to students and faculty. It should:
- Inform them about the upcoming event
- Mention the event name, date, and organizing department
- Encourage attendance

The \"event_highlights\" field should contain a formal description (3-4 sentences) covering:
- What the event is about (topic/theme)
- Who the guest speaker(s) is and their credentials
- Key activities or sessions planned
- What attendees will gain from the event

Use professional, academic language appropriate for a college notice board.";

        $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $payload = [
            "model" => "openai/gpt-oss-120b:free",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey,
            "HTTP-Referer: http://localhost",
            "X-Title: Event Reports App",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("OpenRouter API Error (Notice): HTTP $httpCode - $response");
            return $this->json(['error' => 'Failed to generate content from AI. Try again later.'], 500);
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```/', '', $content);
        $content = trim($content);

        $jsonResult = json_decode($content, true);

        if (!$jsonResult) {
            error_log("OpenRouter returned invalid JSON (Notice): $content");
            return $this->json(['error' => 'AI returned malformed data. Please try again.'], 500);
        }

        return $this->json($jsonResult);
    }
}
