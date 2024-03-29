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
	RangeControl,
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
 * Grab any custom components we are using in this block.
 */
import ActivityList from './components/activity-list';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

const Edit = (props) => {
	const { attributes, setAttributes, isSelected } = props;
	const { currentMP, noOfEntries } = attributes;
	const [ listOfMPs, setListOfMPs ] = useState([]);
	const [ activity, setActivity ] = useState([]);

	const MPsForSelect = (mpsList) => {
		return [
			{
				label: __('Select an MP'),
				value: null,
			},
			...Object.values(mpsList).map((mp) => {
				return {
					label: mp.name,
					value: mp.person_id,
				};
			}),
		];
	};

	// Get the list of MPs on mount.
	useEffect(() => {
		apiFetch({ path: '/twfy/v1/get_mps_names_for_dropdown' }).then(
			(response) => setListOfMPs(response)
		);
	}, [ setListOfMPs ]);

	// Refresh activity when the MP or limit changes.
	useEffect(() => {
		apiFetch({
			path:
				'/twfy/v1/get_mp_details_for_activity/' +
				currentMP +
				'/' +
				noOfEntries,
		}).then((response) => setActivity(response));
	}, [ currentMP, noOfEntries ]);

	return (
		<Fragment>
			{ isSelected && (
				<Placeholder>
					{ !listOfMPs && <Spinner /> }
					{ listOfMPs && !!listOfMPs.length && (
						<SelectControl
							label={ __('Select an MP') }
							value={ currentMP }
							options={ MPsForSelect(listOfMPs) }
							onChange={ (_currentMP) =>
								setAttributes({
									currentMP: parseInt(_currentMP),
								})
							}
						/>
					) }
					{ listOfMPs && !listOfMPs.length && __('No MPs list found.') }
				</Placeholder>
			) }

			{ activity && <ActivityList activity={ activity } /> }

			<InspectorControls>
				<PanelBody title={ __('MPs Recent Activity') }>
					<RangeControl
						label={ __('Number of items to show') }
						value={ noOfEntries }
						onChange={ (_noOfEntries) =>
							setAttributes({ noOfEntries: _noOfEntries })
						}
						min={ 1 }
						max={ 100 }
						required
					/>
				</PanelBody>
			</InspectorControls>
		</Fragment>
	);
};

Edit.propTypes = {
	attributes: PropTypes.shape({
		currentMP: PropTypes.number,
		noOfEntries: PropTypes.number,
	}).isRequired,
};

export default Edit;
