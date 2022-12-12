<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



## Livewire Datatables

JavaScript frameworks are very powerful, but working with them is very complicated. But you can use Livewire to build many web application components. Like the data table.
However, there are also JavaScript plugins such as Datatables. But they also have limitations.
You can easily display the data of your Laravel application using Livewire.


## Features

+ Display and sort data based on the selected column.
+ Pagination
+ Search through the data. Even when you are using eloquent Laravel relationships.
+ Using multiple tables on one page without conflict.
+ Clean components and coding


## Installation and preparation
In this example, we have created a Laravel project that contains a list of users with their cars.
So, we need two models for this.

```
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```
```
Schema::create('cars', function (Blueprint $table) {
    $table->id();
    $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
    $table->string('name')->unique();
    $table->decimal('price', 9, 3);
    $table->timestamps();
});
```
After creating models, we create factories:
<br>
users:
```
public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
```
cars:
```
public function definition()
    {
        static $owner_id = 1;
        return [
            'name' => fake()->unique()->word(),
            'price' =>  (int) rand(30000, 100000),
            'owner_id' => $owner_id++,
        ];
    }
```
And finally in the file databaseSeeder:
```
\App\Models\User::factory(50)->create();
\App\Models\Car::factory(50)->create();
```
Now we prepare the tables:
```
php artisan migrat:fresh --seed
```
If you have not installed Livewire, install it with the following command:
```
composer require livewire/livewire
```
To work with Livewire, be sure to visit its documentation:<br>
https://laravel-livewire.com/docs/2.x/quickstart<br>
Create the basic HTML template in an index file
Don't forget the livewire directives:
```
@livewireStyles
@livewireScripts
```
In the head and at the end of the body.
We also used bootstrap.
Now it's time to build the livewire component. Use the command below:
```
php artisan make:livewire UserCarsTbl
```
You can use any other name instead of UserCarsTbl
We have in the livewire app component file:
```
<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserCarsTbl extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $tableName = 'table';
    public $search;
    public $showPerPage = 10;
    public $orderBy = 'id';
    public $orderMode = 'ASC';
    public $no;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function sortBy($col, $mode){
        $this->orderBy = $col;
        $this->orderMode = $mode;
        $this->resetPage();
    }
    public function updatingshowPerPage()
    {
        $this->resetPage();
    }

    private function bySearch(){
        return User::where('name', 'like', "%$this->search%")
        ->orWhere('email', 'like', "%$this->search%")
        ->orWhereRelation('car', 'name', 'like', "%$this->search%")
        ->orWhereRelation('car', 'price', 'like', "%$this->search%");
    }
    public function render()
    {
        $results = new User();
        if($this->search){
            $results = $this->bySearch();
        }
        $results = $results->orderBy($this->orderBy, $this->orderMode)->paginate($this->showPerPage);
        $this->no = $results->firstItem();
        return view('livewire.user-cars-tbl', compact('results'));
    }
    public function hydrate(){
        $this->dispatchBrowserEvent('sortOk');
    }
}
```
And We have in the livewire blade component file:
```
<div class="table-responsive" id="{{$tableName}}">
    <div class="row justify-content-around">
        <div class="col-md-6 my-3">
            <span class="d-inline-flex justify-content-end">
                show
                <select class="mx-1" wire:model="showPerPage">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                entries
            </span>
        </div>
        <div class="col-md-6 my-3 text-end">
            <span class="d-inline-flex justify-content-end">
                Search :
                <input type="search" placeholder="Search" wire:model="search">
            </span>
        </div>
    </div>
    <table class="table table-sm table-striped table-hover text-center" style="table-layout: fixed">
        <thead>
            <tr>
                <th data-col="id">#</th>
                <th data-col="name">User</th>
                <th>Car Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $res)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$res->name}}</td>
                <td>{{$res->car->name ?? '-'}}</td>
                <td>{{number_format($res->car->price) ?? '-'}} $</td>
            </tr>
            @empty
            <tr>
                <td colspan="4">No Items Found!</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{$results->links()}}
    <script>
        window.addEventListener('DOMContentLoaded', function(e){
            var {{$tableName}}_funcs = new function(){
                let tableContainer = document.getElementById('{{$tableName}}');
                var currentElement = tableContainer.querySelector('[data-col="{{$orderBy}}"]');
                var currentMode = '{{$orderMode}}';
                set_sort_btn_icon();
                let ths = tableContainer.getElementsByTagName('th');
                Array.from(ths).forEach(function(th) {
                    th.addEventListener('click', function(e){
                        var elem = e.target;
                        var col = elem.dataset.col;
                        if(!col){
                            return;
                        }
                        if(currentElement !== elem){
                            mode = 'ASC';
                        }else{
                            if(currentMode == 'ASC'){
                                mode = 'DESC';
                            }else{
                                mode = 'ASC';
                            }
                        }
                        currentMode = mode;
                        currentElement = elem;
                        @this.sortBy(col, mode);
                    });
                });
                window.addEventListener('sortOk', event => {
                    set_sort_btn_icon();
                });
                function set_sort_btn_icon(){
                    if(currentMode == 'ASC'){
                        currentElement.classList.add('before-on');
                    }else{
                        currentElement.classList.add('after-on');
                    }
                }
            }
        });
    </script>
</div>
```
Very, very simple, no explanation is needed...<br>
If you don't want to use Livewire's default theme for the pagination section, don't write 
```
protected $paginationTheme = 'bootstrap';
```
Please see this link:<br>
https://laravel-livewire.com/docs/2.x/pagination
<br>
If you want to use this component multiple times on the same page, the table name is mandatory. It is necessary to use a unique name for each table.
```
Public $tableName = table;
```
For information about the functions used in this component, refer to Lifecycles in the Livewire documentation.
https://laravel-livewire.com/docs/2.x/lifecycle-hooks

```
class UserCarsTbl extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $tableName = 'table';

```
When multiple components are used on a page, we write JavaScript functions like this to avoid conflicts.
```
 var {{$tableName}}_funcs = new function(){
    let tableContainer = document.getElementById('{{$tableName}}');
    .
    .
    .
```
On the index page:<br>
We need these styles for table sort buttons:
```
<style>
    th{
        position: relative !important;
        cursor: pointer;
    }
    th::after, th::before{
        position: absolute;
        display: block;
        opacity: .125;
        right: 10px;
        line-height: 12px;
        font-size: .8em;
    }
    th::before{
        content: "▲";
        bottom: 50%;
    }
    th::after{
        content: "▼";
        top: 50%;  
    }
    .after-on::after, .before-on::before{
        opacity: 1;
    }
</style>
```
Now it's time to call the component:
```
<div class="container-fluid">
    <div class="row mt-5">
        <h1 class="d-block text-center">Users with their car</h1>
        <livewire:user-cars-tbl tableName="myTbl">
        {{-- <livewire:user-cars-tbl tableName="x"> --}}
    </div>
</div>
```
## Eventually
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

There is only one problem!<br>
How can we use the sortBy function for columns that called through relations such as Price?