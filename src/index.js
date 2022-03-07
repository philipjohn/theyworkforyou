/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';

/**
 * Set up our block registration settings.
 */
const settings = {
	apiVersion: 1,

	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	save: () => null, // Use view.php instead.

	attributes: {
		currentMP: {
			type: 'integer',
		},
		noOfEntries: {
			type: 'integer',
			default: 5,
		},
	},
	supports: {
		html: false,
		align: ['left', 'center', 'right', 'wide', 'full'],
	},
};

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType('theyworkforyou/mps-recent-activity', settings);
