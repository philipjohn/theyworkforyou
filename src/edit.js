/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Fragment, useState, useEffect } from '@wordpress/element';
import {
	Placeholder,
	SelectControl,
	Spinner,
	PanelBody,
	RangeControl
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import PropTypes from 'prop-types';
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

const Edit = ( props ) => {
	const { attributes, setAttributes, isSelected } = props;
	const { currentMP, noOfEntries } = attributes;
	const [ listOfMPs, setListOfMPs ] = useState([]);
	const [ activity, setActivity ] = useState([]);

	const getListOfMPs = () => {
		apiFetch( { path: '/twfy/v1/get_mps_names_for_dropdown' } ).then( response =>
			setListOfMPs( response )
		);
	}

	const getActivity = () => {
		apiFetch( { path: '/twfy/v1/get_mp_details_for_activity/' + currentMP + '/' + noOfEntries } )
			.then( response =>
				setActivity( response )
			);
	}

	const MPsForSelect = listOfMPs => {
		return [
			{
				label: __('Select an MP'),
				value: null,
			},
			...Object.values( listOfMPs ).map( mp => {
				return {
					label: mp.name,
					value: mp.person_id,
				}
			} )
		];
	}

	useEffect( () => {
		getListOfMPs()
	}, [ listOfMPs ] )

	useEffect( () => {
		getActivity()
	}, [ currentMP, noOfEntries ] )

	return (
		<Fragment>
			{ isSelected && (
				<Placeholder>
					{ ! listOfMPs && <Spinner /> }
					{ listOfMPs && !! listOfMPs.length && (
						<SelectControl
							label={ __( 'Select an MP' ) }
							value={ currentMP }
							options={ MPsForSelect( listOfMPs ) }
							onChange={ _currentMP => setAttributes( { currentMP: parseInt( _currentMP ) } ) } />
					) }
					{ listOfMPs && ! listOfMPs.length && (
						__( 'No MPs list found.' )
					) }
				</Placeholder>
			) }
			
			{ activity && ( 
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
			) }
			
			<InspectorControls>
				<PanelBody title={ __('MPs Recent Activity') }>
					<RangeControl
						label={ __('Number of items to show') }
						value={ noOfEntries }
						onChange={ _noOfEntries => setAttributes( { noOfEntries: _noOfEntries } ) }
						min={ 1 }
						max={ 100 }
						required />
				</PanelBody>
			</InspectorControls>
		</Fragment>
	)
}

Edit.propTypes = {
	attributes: PropTypes.shape(
		{
			currentMP: PropTypes.number,
			noOfEntries: PropTypes.number
		}
	).isRequired
}

export default Edit;