# Authentication : User guide

The security system is configured in app/config/security.yml.
Pages are restricted to authenticated users only :

```yaml
# app/config/security.yml 
firewalls:
    main:
        pattern: ^/
```

The users will authenticate with a login form (see [SecurityController:loginAction](https://github.com/Maxxxiimus92/p8_todolist_app/blob/master/src/AppBundle/Controller/SecurityController.php#L14)) :

```yaml
# app/config/security.yml
firewalls:
    main:
        anonymous: ~
        pattern: ^/
        form_login:
            login_path: login
            check_path: login_check
            always_use_default_target_path:  true
            default_target_path:  /
        logout: ~
```

The login form url access is not restricted, anyone can login :

```yaml
# app/config/security.yml
access_control:
    - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
```

Only ROLE_ADMIN users can access /users* routes :

```php
// src/AppBundle/Controller/UserController.php

class UserController extends Controller
{
    /**
     * @Route("/users", name="user_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listAction()
```

The users are loaded from the database :

```yaml
# app/config/security.yml
providers:
    doctrine:
        entity:
            class: AppBundle:User
            property: username
```
