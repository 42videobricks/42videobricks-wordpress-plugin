<?php
function videobricks_api_settings_page() {
  if(isset($_POST['video_bricks']['api_key']) && isset($_POST['video_bricks']['environment'])):
    update_option("videobricks_api_key", sanitize_text_field($_POST['video_bricks']['api_key']));
    update_option("videobricks_env", sanitize_text_field($_POST['video_bricks']['environment']));
  endif; ?>
  <h2>42videobricks API key</h2>
  <p><strong>42videobricks</strong> handles the complexity of video for you: no infrastructure required, no CapEx, no complexity!</p>
    <p>Simply add our embed code to your WordPress site or service to add video.</p>
    <p>If you don’t already have an API Key, you can sign up for a free sandbox account at  <a target="_blank" href="https://admin.42videobricks.com/">42videobricks.com</a>.</p>
  <form action="" method="post">
  <input id='input-settings' name='video_bricks[api_key]' type='text' value='<?php echo(esc_html(get_option("videobricks_api_key"))) ?>' />
      <label>Select your environment: </label>
      <select name='video_bricks[environment]' id="env">
          <option <?php if(esc_html(get_option('videobricks_env')) === 'sandbox'){echo("selected");}?> value="sandbox">Sandbox</option>
          <option <?php if(esc_html(get_option('videobricks_env')) === 'staging'){echo("selected");}?> value="staging">Staging</option>
          <option <?php if(esc_html(get_option('videobricks_env')) === 'production'){echo("selected");}?> value="production">Production</option>
      </select>
      <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
  </form>
  <?php 
  // Verify init request API.
  $response = videobrickswp_verify_request();
  if(!$response): ?>
    <p class="videobricks-error-message">Api key is not added/valid. Please fill in a correct apikey</p>
    <?php return;
  endif;
  ?>
<?php }
