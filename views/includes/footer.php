<footer class="kse-footer">

  <div class="footer-container">

    <!-- LEFT : LOGO + ADDRESS -->
    <div class="footer-col">
      <div class="footer-logo">
  <?php $base = Url::getBaseUrl(); ?>
      <img src="<?= $base ?>/public/images/keystone_logo.jpeg" alt="KSE Logo">
        <h3>Keystone School of Engineering</h3>
        <p>Approved by AICTE, Govt. of Maharashtra</p>
      </div>

      <p>
        Keystone Campus, Near Handewadi Chowk,<br>
        Pune – 412308
      </p>

      <p>
        📞 9922887755 / 9922550060 <br>
        ✉ foundation@shalaka.org <br>
        🌐 www.keystoneschoolofengineering.com
      </p>
    </div>



    <!-- RIGHT : PORTAL INFO -->
    <div class="footer-col">
      <h4>Event Management Portal</h4>
      <p>Smart Event Documentation System</p>

      <ul>
        <li><a href="mailto:foundation@shalaka.org">Contact Admin</a></li>
        <li><a href="<?= Url::to('help') ?>">Help & Documentation</a></li>
      </ul>
    </div>

  </div>

  <!-- BOTTOM BAR -->
  <div class="footer-bottom">
    © <?= date("Y"); ?> Keystone School of Engineering | Event Management Portal
  </div>

</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= $base ?>/public/js/clientSideValidation.js"></script>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

window.editors = window.editors || {};
document.querySelectorAll('.ckeditor-field').forEach(function (element) {
    CKEDITOR.ClassicEditor
        .create(element, {
            toolbar: {
                items: [
                    'heading', '|', 'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'alignment:left', 'alignment:center', 'alignment:right', 'alignment:justify', '|',
                    'indent', 'outdent', '|', 'blockQuote', '|', 'undo', 'redo'
                ],
                shouldNotGroupWhenFull: false
            },
            alignment: {
                options: ['left', 'center', 'right', 'justify']
            },
            removePlugins: [
                'CKBox', 'CKFinder', 'EasyImage',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments',
                'TrackChanges', 'TrackChangesData', 'RevisionHistory',
                'Pagination', 'WProofreader', 'MathType', 'SlashCommand',
                'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        })
        .then(editor => {
            // Store instance by the textarea's name attribute
            if (element.name) {
                window.editors[element.name] = editor;
            }
        })
        .catch(error => {
            console.error(error);
        });
});

});
</script>

</body>
</html>
