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
    
### /v1/users/{user}/top_three_maintenance_items_today
    Returns the top 3 maintenance items due today for given user
    
### /v1/users/{user}/section/{section}/top_three_maintenance_items_today
    Returns the top 3 maintenance items due today for given user and section
    
### '/v1/sections/{section}/maintenance_items
    Returns maintenance items belonging to a given section
    
    
## POST

### /v1/users
	Create new User.
    * Expected input: CreateUserRequest (name, email, role)

### /v1/users/{user}/score
	Update user's current score
    * Expected input: score
