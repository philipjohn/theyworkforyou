import React from 'react';

const ActivityListItem = ({ item }) => {
	return (
		<li className="item">
			<span className="date">
				<a href="{ item.url }">
					{item.date}
					{item.time ? (
						<span className="time"> at {item.time}</span>
					) : null}
				</a>
				&nbsp;in&nbsp;
				<span className="context">{item.context}</span>
			</span>
			<br />
			<span className="body">{item.body}</span>
		</li>
	);
};

export default ActivityListItem;
