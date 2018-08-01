
require(['bootstrap-switch'], function(){
  $('.flagCheckbox').bootstrapSwitch();
  $('.flagCheckbox').on('switchChange.bootstrapSwitch', function(event, state) {
    if(state) {
      $.ajax($(this).data('on-url'));
    } else {
      $.ajax($(this).data('off-url'));
    }
    //console.log(this); // DOM element
    //console.log(event); // jQuery event
    //console.log(state); // true | false
  });
});