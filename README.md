# custodia

## Firebase Laravel Integration
The following must be set in .env:
```
FIREBASE_DATABASE_URL=
FIREBASE_PROJECT_ID=
FIREBASE_PRIVATE_KEY_ID=
FIREBASE_PRIVATE_KEY=
FIREBASE_CLIENT_EMAIL=
FIREBASE_CLIENT_ID=
FIREBASE_CLIENT_x509_CERT_URL=
```

This information is obtained from the Firebase Service Account JSON cred file.

SDK Docs: https://firebase-php.readthedocs.io/en/latest/



# REST API Endpoints

## GET

### /v1/users/{user}/score
    Returns the current score for a given user id
    
### /v1/users/{user}/done_maintenance_items
    Returns the list of done maintenance items for a given user id
    
### /v1/users/{user}/ignored_maintenance_items
    Returns the list of ignored maintenance items for a given user id
    
### /v1/users/{user}/maintenance_item/{maintenance_item}/done
    Returns if a given maintenance item is done by a given user
    
### /v1/users/{user}/maintenance_item/{maintenance_item}/ignored
    Returns if a given maintenance item is ignored by a given user
    
### /v1/users/{user}/top_three_maintenance_items_today
    Returns the top 3 maintenance items due today for given user
    
### /v1/users/{user}/outdoor_spaces
    Returns the list of outdoor_spaces for a given user id
    
### /v1/users/{user}/driveways
    Returns the list of driveways for a given user id
    
### /v1/users/{user}/home_features
    Returns the list of home_features for a given user id
    
### /v1/users/{user}/mobility_issues
    Returns the list of mobility_issues for a given user id
    
### /v1/users/{user}/section/{section}/top_three_maintenance_items_today
    Returns the top 3 maintenance items due today for given user and section
    
### '/v1/sections/{section}/maintenance_items
    Returns maintenance items belonging to a given section
    
    
## POST

### /v1/auth
	Authenticate user via phone or google auth. Return user ID.
    * Expected input: phone (phone number)
    		      gauth (user gauth id)

### /v1/users
	Create new User.
    * Expected input: CreateUserRequest (name, email, role)

### /v1/users/{user}/score
	Update user's current score
    * Expected input: score
    
### /v1/users/{user}/outdoor_spaces
    	Set the list of outdoor_spaces for a given user id
    * Expected input: outdoor_spaces (a list of outdoor space IDs outdoor_spaces[])
    
### /v1/users/{user}/driveways
    	Set the list of driveways for a given user id
    * Expected input: driveways (a list of driveways IDs driveways[])
    
### /v1/users/{user}/home_features
    	Set the list of home_features for a given user id
    * Expected input: home_features (a list of home feature IDs home_features[])
    
### /v1/users/{user}/mobility_issues
    	Set the list of mobility_issues for a given user id
    * Expected input: mobility_issues (a list of mobility issue IDs mobility_issues[])
    
### /v1/users/{user}/maintenance_item/{maintenance_item}/done
    	Set the a given maintenance item to be done for a given user id
    * Expected input: none
    
### /v1/users/{user}/maintenance_item/{maintenance_item}/ignored
    	Set the a given maintenance item to be ignored for a given user id
    * Expected input: none
