import 'browsernizr/test/es6/promises';
import Modernizr from 'browsernizr';

import es6Promise from 'es6-promise';
if(!Modernizr.promises) {
	es6Promise.polyfill();
}
