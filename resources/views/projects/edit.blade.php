@extends('layouts.app')

@section('content')

   
    <div class="lg:w-1/2 lg:mx-auto bg-white p-6 md:py-12 md:px-16 rounded shwadow">
   
        <h1 class="text-2xl font-normal mb-10 text-center"
        >
        Editer un Project
        </h1>

        <form action="{{ $project->path() }}" 
            method="POST" 
        >
        @method('PATCH')
        @include('projects._form', ['buttonText' => 'Update Project'])
        
        </form>

    </div>
    
@endsection