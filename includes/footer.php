</div><!-- /.app -->

<div class="toast-wrap" id="toastWrap"></div>

<script src="js/data.js"></script>
<script src="js/main.js"></script>
<?php if (!empty($pageScripts)): foreach ($pageScripts as $src): ?>
<script src="<?= htmlspecialchars($src) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
