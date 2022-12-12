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
