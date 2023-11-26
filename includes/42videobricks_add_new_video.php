<?php
function videobrickswp_add_new_video() {
    // Verify init request valid.
  $response = videobrickswp_verify_request();
  if(!$response): ?>
    <p class="videobricks-error-message">Api key is not added/valid, please go to <a href="admin.php?page=videobricks-settings">settings</a> page and update it with correct one</p>
    <?php return;
  endif;
  ?>

  <div class="wrap">
    <h2><?php echo(esc_html(get_admin_page_title())) ?></h2>
  </div>

  <div id="dragAndDropContainer" class="dragAndDropContainerWrapper">
    <div id="video-drop-container-gret"></div>
    <h2 class="upload-instructions drop-instructions">Drop files to upload</h2>
    <p class="upload-instructions drop-instructions">or</p>
    <label for="videobricksFileInput" id="video-file-label">
		  Select files
    </label>
      <p>File formats: avi, mov, mp4, mpeg, mpg, mxf, ts</p>

  </div>

    <input type="file" id="videobricksFileInput" accept="video/x-msvideo,video/msvideo,video/avi,application/x-troff-msvideo,video/quicktime,video/mp4,video/mpeg,video/mpg,application/mxf,video/MP2T" />

  <div id="action-upload">
	<input type="file" id="video-file" accept="mov">
  </div>
  <div id="videobricks-upload-information"></div>
  <div id="videobricks-upload-progress-bar">
    <progress id="progress-tracker" value="0" max="100"></progress>
  </div>
  <div id="videobricks-video-information"></div>

  <script>
  dragAndDropContainer.ondragover = dragAndDropContainer.ondragenter = function(evt) {
    document.getElementById("dragAndDropContainer").style.borderColor = "#2271b1";
    evt.preventDefault();
  };
  dragAndDropContainer.ondragleave = dragAndDropContainer.ondragleave = function(evt) {
    document.getElementById("dragAndDropContainer").style.borderColor = "#c3c4c7";
    evt.preventDefault();
  };

  dragAndDropContainer.ondrop = function(evt) {
    videobricksFileInput.files = evt.dataTransfer.files;
    const dT = new DataTransfer();
    dT.items.add(evt.dataTransfer.files[0]);
    videobricksFileInput.files = dT.files;
    evt.preventDefault();
  };
  </script>
<?php }
