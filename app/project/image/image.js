require(['jquery', 'jquery-form'], function($){

  var progressBar = '.progress-bar';

  var progressBarFormImage = '#image form';
  $(progressBarFormImage).ajaxForm({
    beforeSend: function() {
      $(progressBarFormImage).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormImage).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormImage).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormImage).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormImage).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
        $(progressBarFormImage).find(':input').attr('disabled', false);
        $(progressBarFormImage).find(':input').val("");
      } else {
        $(progressBarFormImage).find(':input').attr('disabled', false);
        $(progressBarFormImage).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });

  var progressBarFormBanner = '#banner form';
  $(progressBarFormBanner).ajaxForm({
    beforeSend: function() {
      $(progressBarFormBanner).find(progressBar).empty().css('width', '0%').addClass('progress-bar-success').removeClass('progress-bar-danger');
      $(progressBarFormBanner).find(':input').attr('disabled', true);
    },
    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      $(progressBarFormBanner).find(progressBar).html(percentVal).css('width', percentVal);
    },
    success: function() {
      var percentVal = '100%';
      $(progressBarFormBanner).find(progressBar).html(percentVal).css('width', percentVal);
    },
    complete: function(xhr) {
      if(xhr.status == 200) {
        $(progressBarFormBanner).find(progressBar).html(xhr.responseText).addClass('progress-bar-success').removeClass('progress-bar-danger');
        $(progressBarFormBanner).find(':input').attr('disabled', false);
        $(progressBarFormBanner).find(':input').val("");
      } else {
        $(progressBarFormBanner).find(':input').attr('disabled', false);
        $(progressBarFormBanner).find(progressBar).html(xhr.responseText).css('width', '100%').addClass('progress-bar-danger').removeClass('progress-bar-success');
      }
    }
  });
});

require(['vendor/editable', 'bootstrap-validator', 'bootstrap-editable', 'bootstrap-select'], function() {
  $('form.validate').validator();
  // bootstrap-editable
  $.fn.editable.defaults.mode = 'popup';
  $.fn.editable.defaults.emptytext = 'Kein Inhalt';

  //$('.editable.selectItemID').editable({source: items});
  $('.editable.selectTargetWidth').editable({source: {"25":"25%", "33":"33%", "50":"50%", "100":"100%" }});
  $('.editable.selectHorizontalAlign').editable({source: {"left":"links", "right":"rechts"}});
});
