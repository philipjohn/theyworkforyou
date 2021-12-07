/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Component, Fragment } from '@wordpress/element';
import {
	Placeholder,
	SelectControl,
	Spinner,
	PanelBody,
	RangeControl
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InspectorControls } from '@wordpress/block-editor';

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
			listofMPs: [],
			currentMP: 0,
			activity: []
		}
	}

	componentDidMount() {
		const { currentMP, noOfEntries } = this.props.attributes;
		this.setState( { currentMP: currentMP } );

		apiFetch( { path: '/twfy/v1/get_mps_names_for_dropdown' } ).then( response =>
			this.setState( { listofMPs: response } )
		);
		apiFetch( { path: '/twfy/v1/get_mp_details_for_activity/' + currentMP + '/' + noOfEntries } )
			.then( response =>
				this.setState( { activity: response } )
			);
	}

	componentDidUpdate( prevProps ) {
		const { currentMP, noOfEntries } = this.props.attributes;

		if ( prevProps.attributes.currentMP !== currentMP ) {
			this.setState( { currentMP: currentMP } );
			apiFetch( { path: '/twfy/v1/get_mp_details_for_activity/' + currentMP + '/' + noOfEntries } )
				.then( response =>
					this.setState( { activity: response } )
				);
		}
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
		const { attributes, setAttributes, isSelected } = this.props;
		const { listofMPs, activity } = this.state;
		const { currentMP, noOfEntries } = attributes;

		return (
			<Fragment>
				{ isSelected && (
					<Placeholder>
						{ ! listofMPs && <Spinner /> }
						{ listofMPs && !! listofMPs.length && (
							<SelectControl
								label={ __( 'Select an MP' ) }
								value={ currentMP }
								options={ this.MPsForSelect( listofMPs ) }
								onChange={ _currentMP => setAttributes( { currentMP: _currentMP } ) }
								/>
						) }
						{ listofMPs && ! listofMPs.length && (
							__( 'No MPs list found.' )
						) }
					</Placeholder>
				) }
				
				<div className="wp-block-theyworkforyou-mps-recent-activity">
					<h2>Recent activity by { activity.fullName } MP</h2>
					<ul className="mps-activity">
						{ activity.items && activity.items.map( item => (
							<li className="item" key={ item.id }>
								<span className="date">
									<a href="{ item.url }">
										{ item.date }
										{ item.time ? <span className="time"> at { item.time }</span> : null }
									</a>
									&nbsp;in&nbsp;
									<span className="context">{ item.context }</span>
								</span><br/>
								<span className="body">{ item.body }</span>
							</li>
						) ) }
					</ul>
				</div>
				
				<InspectorControls>
					<PanelBody title={ __('MPs Recent Activity') }>
						<RangeControl
							label={ __('Number of items to show') }
							value={ noOfEntries }
							onChange={ _noOfEntries => setAttributes( { noOfEntries: _noOfEntries } ) }
							min={ 1 }
							max={ 100 }
							required
							/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		)
	}
}

export default Edit;