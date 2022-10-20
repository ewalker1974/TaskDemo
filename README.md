# Task Demo Applcation
## Deployment
go to docker directory\
docker-compose up -d

### Composer
docker-compose exec -u www-data -w /var/www/html php-td composer install

### Prepare project
cp .env.example .env\
since it is provided docker env you don't need to modify .env

### Database migrations
docker-compose exec -u www-data -w /var/www/html php-td php artisan migrate\
docker-compose exec -u www-data -w /var/www/html php-td php artisan db:seed

### Resources
all params are query params (even for POST, PUT and PATCH methods)
<ul>
<li>
GET /api/v1.0/users <br>
Returns list of all users
<h4> Params </h4>
<ul>
<li>
page: page number
</li>
<li>
size: page size 
</li>
</ul>
</li>
<li>
GET /api/v1.0/{users_id}/tasks<br>
Returns list of tasks assigned to particular user
<h4> Params </h4>
<ul>
<li>
page: page number
</li>
<li>
size: page size 
</li>
</ul>
</li>
<li>
GET /api/v1.0/tasks<br>
Returns list of all tasks
<h4> Params </h4>
<ul>
<li>
page: page number
</li>
<li>
size: page size 
</li>
</ul>
</li>
<li>
GET /api/v1.0/tasks/{task_id}
<br>
Returns information of particular task
<h4> Params </h4>
No
</li>
<li>
POST /api/v1.0/tasks
<br>
Creates a new task
<h4> Params </h4>
<ul>
<li>
name: name of task:string 
</li>
<li>
due_to: due date: date
</li>
<li>
status: status: one of 'assigned', 'in progress', 'testing', 'done'
</li>
<li>
assigned_user_id: id of user to whom task is assigned: string should be id of existing user 
</li>
</ul>
</li>
<li>
PUT/PATCH /api/v1.0/tasks/{task_id}
<br>
Updates a task
<h4> Params </h4>
<ul>
<li>
name: name of task:string 
</li>
<li>
due_to: due date: date
</li>
<li>
status: status: one of 'assigned', 'in progress', 'testing', 'done'
</li>
<li>
assigned_user_id: id of user to whom task is assigned: string should be id of existing user 
</li>
</ul>
<li>
GET /api/v1.0/tasks/{task_id}/changes
<br>
Returns change log of a task
<h4> Params </h4>
<ul>
<li>
page: page number
</li>
<li>
size: page size 
</li>
</ul>
</li>
</ul>
