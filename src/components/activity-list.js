import React from 'react';
import ActivityListItem from './activity-list-item';

const ActivityList = ({ activity }) => {
	return (
		<div className="wp-block-theyworkforyou-mps-recent-activity">
			<h2>Recent activity by {activity.fullName} MP</h2>
			<ul className="mps-activity">
				{activity.items &&
					activity.items.map((item) => (
						<ActivityListItem item={item} key={item.id} />
					))}
			</ul>
		</div>
	);
};

export default ActivityList;
