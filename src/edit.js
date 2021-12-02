/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component, Fragment } from '@wordpress/element';
import {
	Placeholder,
	SelectControl,
	Spinner
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

class Edit extends Component {

	/**
	 * Constructor.
	 */
	constructor() {
		super( ...arguments );
		this.state = {
			listofMPs: []
		}
	}

	componentDidMount() {
		apiFetch( { path: '/twfy/v1/get_mps_names_for_dropdown' } ).then( response =>
			this.setState( { listofMPs: response, initialUpdate: true } )
		);
	}

	MPsForSelect = listofMPs => {
		return [
			{
				label: __('Select an MP'),
				value: null,
			},
			...Object.values( listofMPs ).map( mp => {
				return {
					label: mp.name,
					value: mp.person_id,
				}
			} )
		];
	}

	render() {
		const { attributes, setAttributes } = this.props;
		const { listofMPs } = this.state;
		const { currentMP } = attributes;

		return (
			<Fragment>
				<Placeholder>
					{ ! listofMPs && <Spinner /> }
					{ listofMPs && !! listofMPs.length && (
						<SelectControl
							label={ __( 'MP' ) }
							value={ currentMP }
							options={ this.MPsForSelect( listofMPs ) }
							onChange={ _currentMP => setAttributes( { currentMP: _currentMP } ) }
							/>
					) }
					{ listofMPs && ! listofMPs.length && (
						__( 'No MPs list found.' )
					) }
				</Placeholder>
			</Fragment>
		)
	}
}

export default Edit;