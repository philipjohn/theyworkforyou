import React from 'react';
import PropTypes from 'prop-types';

const ActivityListItem = ( { item } ) => {
	return (
		<li className="item" key={ item.id } >
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
	)
}

ActivityList.propTypes = {
	item: PropTypes.object.isRequired
}

export default ActivityListItem; 