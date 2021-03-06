<?php use_helper('I18N', 'Text') ?>
<?php use_javascript('/rtCorePlugin/js/main.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.ui.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/ajaxupload/ajaxupload.js') ?>
<?php $panel_suffix = isset($panel_suffix) ? $panel_suffix : rand() ?>
<?php $description_text = __('Description goes here...') ?>
<fieldset class="rt-core-upload">
  <legend><?php echo __('Attached Files') ?></legend>
  <?php if($object->isNew()): ?>
  <p><?php echo __('Please create page before adding assets.'); ?></p>
  <?php else: ?>
    <ul class="rt-core-upload-panel clearfix" id="rtCoreUploadPanel<?php echo $panel_suffix ?>">
      <?php foreach($object->getAssets() as $asset): ?>
        <?php include_partial('rtAsset/asset_row', array('asset' => $asset)); ?>
      <?php endforeach; ?>
    </ul>
    <p>
      <button id="uploadImageButton<?php echo $panel_suffix ?>"><?php echo __('Upload a file') ?></button>
      <span id="rtCoreUploadPanelMessage<?php echo $panel_suffix ?>"></span>
    </p>
    <?php endif; ?>
</fieldset>
<?php echo $form['_csrf_token']->render(); ?>
<?php echo $form['model']->render(); ?>

<?php if(!$object->isNew()): ?>
<script type="text/javascript">
$(document).ready(function() {

  $('#uploadImageButton<?php echo $panel_suffix ?>').button({
    icons: { primary: 'ui-icon-transfer-e-w' }
  });

  deleteAsset = function(assetId)
  {
    var assetRowId = '#rtAttachedAsset'+assetId;
    $(assetRowId).fadeTo('fast', 0.5);
    $.ajax({
      dataType: 'json',
      data: {
        id : assetId
      },
      url: '<?php echo url_for('@rt_asset_delete?sf_format=json') ?>',
      success: function(data) {
        if(data.status === 'success')
        {
          $(assetRowId).hide();
        }
      }
    });
    return false;
  }

	$(function() {
		$("#rtCoreUploadPanel<?php echo $panel_suffix ?>").sortable(
      {
        opacity      : 0.7,
        update : function (event, ui) {
          $.ajax({
            dataType: 'json',
            data: {
              order : $('#rtCoreUploadPanel<?php echo $panel_suffix ?>').sortable('toArray')
            },
            url: '<?php echo url_for('@rt_asset_reorder?sf_format=json') ?>',
            success: function(data) {}
          });
        }
      }
    );
	});
  var button = $('#uploadImageButton<?php echo $panel_suffix ?>');
  var message = $('#rtCoreUploadPanelMessage<?php echo $panel_suffix ?>');
  new AjaxUpload(button,{
    action: '<?php echo url_for('@rt_asset_upload') ?>',
    name: 'rt_asset[filename]',
    data: {
      'rt_asset[model_id]'   : '<?php echo $object->getId() ?>',
      'rt_asset[model]'      : '<?php echo get_class($sf_data->getRaw('object')) ?>',
      'rt_asset[_csrf_token]': '<?php echo $form['_csrf_token']->getValue(); ?>'
    },
    onSubmit : function(file, ext){
      message.text('<?php echo __('Uploading') ?>').fadeIn(0);
      this.disable();
    },
    onComplete: function(file, response){
      this.enable();
      $("#rtCoreUploadPanel<?php echo $panel_suffix ?>").append(response);
      message.text('<?php echo __('Upload complete') ?>').fadeOut(4000);
    }
  });
});
</script>
<?php endif; ?>
