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