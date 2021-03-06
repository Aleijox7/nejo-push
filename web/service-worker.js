/*
*
*  Push Notifications codelab
*  Copyright 2015 Google Inc. All rights reserved.
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      https://www.apache.org/licenses/LICENSE-2.0
*
*  Unless required by applicable law or agreed to in writing, software
*  distributed under the License is distributed on an "AS IS" BASIS,
*  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
*  See the License for the specific language governing permissions and
*  limitations under the License
*
*/

/* eslint-env browser, serviceworker, es6 */

self.addEventListener('install', function(event) {
  self.skipWaiting();
});


self.addEventListener('push', function(event) {
try {
	var jsonData = event.data.text();
	var objectData = JSON.parse(jsonData);

	options = objectData;
	console.log(options);
	// options['body'] = body;
	if (objectData['title'] != null) {
		title = objectData['title'];
	} else {
		title = "DEFAULT TITLE";
	}

	event.waitUntil(
	self.registration.showNotification(title, options)
	  );
} catch(e) {
}	
});

self.addEventListener('notificationclick', function(event) {
	if (!event.action) {
		event.notification.close();
		return;
	}

	actions = event.currentTarget.options.actions;
	switch (event.action) {
		case 'cancel-service-action':
		break;
		case 'go-to-service-action':
		for (var i = actions.length - 1; i >= 0; i--) {
			if (actions[i].action == event.action) {
				url = actions[i].url;
				clients.openWindow(url);
			}
		}
		break;
		default:
		for (var i = actions.length - 1; i >= 0; i--) {
			if (actions[i].action == event.action) {
				url = actions[i].url;
				clients.openWindow(url);
			}
		}
		break;
	}

	event.notification.close();
});



// self.addEventListener('push', function(event) {


// 	// data = event.data.json();

// 	console.log(event);
// 	return true;
// 	// return true;

// 	const title = 'TEST';
// 	const options = {
// 		body: 'TEST',
// 		// icon: data.icon,
// 		// badge: data.badge,
// 		// image: data.image,
// 		//vibrate: [300, 100, 100, 300],
		// actions: [
		// 	{
		// 		action: 'go-to-service-action',
		// 		title: 'Asignar a los mensajeros',
		// 		// icon: data.assign
		// 	},
		// 	{
		// 		action: 'cancel-service-action',
		// 		title: 'Cancelar',
		// 		// icon: data.cancel
		// 	},
		// ],
		// silent: true,
		// renotify: true,
		// tag: 'go-to-service-action',
// 	};
	
// 	self.addEventListener('notificationclick', function(event) {
// 		if (!event.action) {
// 			// clients.openWindow(appUrl);
// 			event.notification.close();
// 			return;
// 		}
		
// 		// appUrl = data.image;

// 		switch (event.action) {
// 			case 'go-to-service-action':
// 			// clients.openWindow(appUrl);
// 			break;
// 			case 'cancel-service-action':
// 			break;
// 			default:
// 			// clients.openWindow(appUrl);
// 			break;
// 		}
		
// 		event.notification.close();
// 	});

// 	const notificationPromise = self.registration.showNotification(title, options);
// 	event.waitUntil(notificationPromise).then(function(){
// 	self.addEventListener('notificationclick', function(event) {
// 		if (!event.action) {
// 			clients.openWindow(appUrl);
// 			event.notification.close();
// 			return;
// 		}
		
// 		appUrl = data.image;

// 		switch (event.action) {
// 			case 'go-to-service-action':
// 			clients.openWindow(appUrl);
// 			event.notification.close();
// 			break;
// 			case 'cancel-service-action':
// 			event.notification.close();
// 			break;
// 			default:
// 			console.log(`Unknown action clicked: '${event.action}'`);
// 			clients.openWindow(appUrl);
// 			event.notification.close();
// 			break;
// 		}
		
// 		event.notification.close();
// 	});
// 	});
// });