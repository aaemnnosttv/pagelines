// Add custom JS here
jQuery('a[rel=twitterpopover]').popover({
  html: true,
  trigger: 'hover',
  placement: 'top',
  content: function(){return '<img src="' + jQuery(this).data('img') + '" />';}
});