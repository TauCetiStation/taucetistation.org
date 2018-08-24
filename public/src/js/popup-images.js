/* import $ from 'zepto'; */
/* does not work with zepto :( */
/*import $ from 'jquery';
import magnificPopup from 'magnific-popup';


$('.js-popupImages').magnificPopup({
  type: 'image'
});*/

import baguetteBox from 'baguettebox.js';

baguetteBox.run('.js-popupImages', {
	animation: 'fadeIn',
	noScrollbars: true,
	overlayBackgroundColor: 'rgba(0,0,0,0.2)',
	captions: function(element) {
		return element.getElementsByTagName('img')[0].alt;
	}
});

/*$.magnificPopup.open({
  items: {
    src: '<div class="white-popup">Dynamically created popup</div>', // can be a HTML string, jQuery object, or CSS selector
    type: 'inline'
  }
});*/