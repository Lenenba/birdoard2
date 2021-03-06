<?php

namespace Tests\Feature;

use App\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;

use function Ramsey\Uuid\v1;

class ManageProjectTest extends TestCase
{
    use  WithFaker, RefreshDatabase;




    /** @test */
    public function guests_cannot_create_projects()
    {
        // $this->withoutExceptionHandling();


        $project = factory('App\Project')->create();


        $this->get('/projects')->assertRedirect('login');
        $this->get('/projects/create')->assertRedirect('login');
        $this->get($project->path())->assertRedirect('login');
        $this->post('/projects', $project->toArray())->assertRedirect('login');
    }

    /** @test */

    public function a_user_can_create_a_project()
    {
        $this->signIn();

        $this->get('/projects/create')->assertStatus((200));


        $attributes = [

                'title' => $this->faker->sentence,
                'description' => $this->faker->sentence,
                'notes' => 'General notes for the project'
        ];


        $response = $this->post('/projects', $attributes);

        $project = Project::where($attributes)->first();

        $this->assertDatabaseHas('projects', $attributes);

        $this->get($project->path())
                ->assertSee($attributes['title'])
                ->assertSee($attributes['description'])
                  ->assertSee($attributes['notes']);
    }


    /** @test */
    public function a_user_can_update_a_project()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)
             ->patch($project->path(), $attributes = ['title' => 'changed', 'description ' => 'changed', 'notes' => 'changed'])
             ->assertRedirect($project->path());

        $this->get($project->path() . '/edit')->assertOk();
        
        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    public function a_user_can_view_thier_project()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)->get($project->path())
                   ->assertSee($project->title)
                   ->assertSee($project->description);
    }


    
    /** @test */
    public function an_authenticate_user_cannot_view_a_project_of_others()
    {
        $project = ProjectFactory::create();

        $this->actingAS($project->owner)->get($project->path())
                   ->assertStatus(403);
    }
    
    
    
    
    /** @test */
    public function a_project_requires_a_title()
    {
        $this->signIn();

        $attributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }



    /** @test */
    public function a_project_requires_a_description()
    {
        $this->signIn();
        
        $attributes = factory('App\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }
}
