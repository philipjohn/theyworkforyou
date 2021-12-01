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
			listofMPs: [
				{
					"member_id": "42733",
					"person_id": "25984",
					"name": "Jill Mortimer",
					"party": "Conservative",
					"constituency": "Hartlepool",
					"office": [
						{
							"dept": "Subsidy Control Bill Committee",
							"position": "Member",
							"from_date": "2021-10-25",
							"to_date": "9999-12-31"
						}
					]
				},
				{
					"member_id": "42732",
					"person_id": "10133",
					"name": "Jeremy Corbyn",
					"party": "Independent",
					"constituency": "Islington North"
				},
				{
					"member_id": "42731",
					"person_id": "25797",
					"name": "Claudia Webbe",
					"party": "Independent",
					"constituency": "Leicester East",
					"office": [
						{
							"dept": "Environmental Audit Committee",
							"position": "Member",
							"from_date": "2020-03-02",
							"to_date": "9999-12-31"
						},
						{
							"dept": "Foreign Affairs Committee",
							"position": "Member",
							"from_date": "2020-05-11",
							"to_date": "9999-12-31"
						},
						{
							"dept": "Committees on Arms Export Controls",
							"position": "Member",
							"from_date": "2020-07-06",
							"to_date": "9999-12-31"
						}
					]
				},
				{
					"member_id": "42077",
					"person_id": "24807",
					"name": "Chi Onwurah",
					"party": "Labour",
					"constituency": "Newcastle upon Tyne Central",
					"office": [
						{
							"dept": "",
							"position": "Shadow Minister (Business, Energy and Industrial Strategy)",
							"from_date": "2020-04-10",
							"to_date": "9999-12-31"
						},
						{
							"dept": "",
							"position": "Shadow Minister (Digital, Culture, Media and Sport)",
							"from_date": "2020-04-10",
							"to_date": "9999-12-31"
						}
					]
				},
				{
					"member_id": "42078",
					"person_id": "24709",
					"name": "Bridget Phillipson",
					"party": "Labour",
					"constituency": "Houghton and Sunderland South",
					"office": [
						{
							"dept": "",
							"position": "Shadow Chief Secretary to the Treasury",
							"from_date": "2020-04-06",
							"to_date": "9999-12-31"
						}
					]
				}
			]
		}
	}

	// componentDidMount() {
	// 	apiFetch( { path: '/twfy/v1/mps' } ).then( response =>
	// 		this.setState( { listofMPs: response.listofMPs, initialUpdate: true } )
	// 	);
	// }

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