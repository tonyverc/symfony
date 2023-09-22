<?php

namespace App\DataFixtures;

use DateTime;
use App\entity\Project;
use App\entity\SchoolYear;
use App\entity\Student;
use App\entity\Tag;
use App\entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
  {
     private $faker;
     private $hasher;
     private $manager;

     public function __construct(UserPasswordHasherInterface $hasher)
     {
         $this->faker = FakerFactory::create('fr_FR');
         $this->hasher = $hasher;
     }

     public static function getGroups(): array
     {
         return [ 'test'];
     }

     public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
        $this->manager = $manager;
        $this->loadTags();
        $this->loadSchoolYears();
        $this->loadProjects();
        $this->loadStudents();
        
    }

    public function loadTags(): void
    {
        //données statique
        $datas= [
                    ['name' => 'HTML',
                    'description' => null,
                    ],  
                    [
                    'name' => 'CSS',
                    'description' => null,
                    ],

                    ['name' => 'JS',
                    'description' => null,
                    ],
                ];
        
                foreach ($datas as $data){
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);

                    //$this->manger->persist($tag);

            }
            $this->manager->flush();

            //données dynamique
            for ($i = 0; $i < 10; $i++){
                $tag = new Tag();
                $words = random_int(1 , 3);
                $tag->setName($this->faker->unique()->sentence($words));
                $words = random_int(8 , 15);
                $tag->setDescription($this->faker->sentence($words));
                
                $this->manager->persist($tag);
            }

            $this->manager->flush();
    }

    public function loadSchoolYears(): void
    {
        //données statique
        $datas= [
                    ['name' => 'Alan Turing',
                    'description' => null,
                     'startDate' => new DateTime('2022-01-01'),
                     'endDate'=> new DateTime('2022-12-31'),
                    ],  
                    [
                    'name' => 'John Von Neumann',
                    'description' => null,
                    'startDate' => new DateTime('2022-06-01'),
                     'endDate'=> new DateTime('2023-05-31'),
                    ],

                    ['name' => 'Brendan Eich',
                    'description' => null,
                    'startDate' => null,
                     'endDate'=> null,
                     ],
                ];
        
        foreach ($datas as $data){
            $schoolYear = new Schoolyear();
            $schoolYear->setName($data['name']);
            $schoolYear->setDescription($data['description']);
            $schoolYear->setStartDate($data['startDate']);
            $schoolYear->setEndDate($data['endDate']);
            
            $this ->manager->persist($schoolYear); 
        }
        $this->manager->flush();

        for ($i = 0; $i < 10; $i++){
             $schoolYear = new schoolYear();

             $words = random_int(2 , 4);
             $schoolYear->setName($this->faker->unique()->sentence($words));

             $words = random_int(8, 15);
             $schoolYear->setDescription($this->faker->optional($weight=0.7)->sentence($words));

             $startDate = $this->faker->dateTimeBetween('-1 year', '-6 months');
             $schoolYear->setStartDate($startDate);

             $endDate = $this->faker->dateTimeBetween('-6 months' , 'now');
             $schoolYear->setEndDate($endDate);

             $this->manager->persist($schoolYear);

        }
            $this->manager->flush();
    }

    public function loadStudents(): void
    {   
        $repository =$this->manager->getRepository(SchoolYear ::class);
        $schoolYears = $repository->findAll();

        $alanTuring = $repository->find(1);
        $johnVonNeuman = $repository->find(2);
        $brendanEich = $schoolYears[2];

        $repository = $this->manager->getRepository(Project ::class);
        $projects = $repository->findAll();

        $siteVitrine = $repository->find(1);
        $wordpress = $repository->find(2);
        $apiRest = $repository->find(3);

        $repository = $this->manager->getRepository(Tag ::class);// recupération de la class tag
        $tags = $repository->findAll(); // recherche de tout les tag dans le repository tag

        $html =$repository->find(1);
        $css =$repository->find(2);
        $js = $repository->find(3);

        // données statiques
        $datas =[
            [
                'email' => 'foo@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname'=> 'foo',
                'lastname'=> 'example',
                'schoolyear'=> $alanTuring ,
                'projects'=>[$siteVitrine], //affecte un seul projet à l'utilisateur
                'tags'=>[$html], // affecte un tag a un student(possible d'en mettre plusieurs)
            ],
            [
                'email' => 'bar@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname'=> 'bar',
                'lastname'=> 'example',
                'schoolyear'=> $johnVonNeuman,
                'projects'=>[$wordpress],
                'tags'=>[$css],
            ],
            [
                'email' => 'baz@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstname'=> 'baz',
                'lastname'=> 'example',
                'schoolyear'=> $brendanEich,
                'projects'=>[$apiRest],
                'tags'=>[$js],
            ],
        ];

        foreach ($datas as $data){

            $user = new User();
            $user ->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user ->setPassword($password);
            $user ->setRoles($data['roles']);

            $this ->manager->persist($user); 

            $student = new student();
            $student->setFirstname($data['firstname']);
            $student->setLastname($data['lastname']);
            $student->setSchoolYear($data['schoolyear']);
            $student->setUser($user);

            // récuperation du premier projet de la liste du student
            $project = $data['projects'][0];
            $student->addProject($project);

            $tag = $data['tags'][0]; //affectation d'un tag à un student avec son index
            $student->addTag($tag);

            $this ->manager->persist($student); 
        }

        $this ->manager->flush();

        //données dynamique
        for ($i=0; $i < 10; $i++) {

            $user = new User();
            $user ->setEmail($this->faker->unique()->safeEmail());
            $password = $this->hasher->hashPassword($user,'123');
            $user ->setPassword($password);
            $user ->setRoles(['ROLE_USER']);

            $this ->manager->persist($user);

            $student = new student();  //création nouveau student
            $student->setFirstname($this->faker->firstName());//genere un nom de famille aléatoire
            $student->setLastname($this->faker->lastName()); //genere un prénom aléatoire

            $schoolYear = $this->faker->randomElement($schoolYears);
            $student->setSchoolYear($schoolYear);

             
            $project = $this->faker->randomElement($projects);
            $student->addProject($project);

            $tag =$this->faker->randomElement($tags);
            $student->addTag($tag); // ajout d'un tag à un student

            $student->setUser($user);

            $this ->manager->persist($student); 
        }
            $this ->manager->flush();
    }
    public function loadProjects(): void
    {
        //fonction qui recupere la liste des tags en entier
        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll(); 

        //récuperation d'un tag à partir de son ID
        $htmlTag = $repository->find(1);
        $cssTag = $repository->find(2);
        $jsTag = $repository->find(3); // ou $jsTag = $tags[2]; pour recuperer le 3eme element de la liste 

        //éléments du code à reutiliser dans les boucles
        $html = $tags[0];
        $html->getName();

        $tags[0]->getName();

        $shortList = $this->faker->randomElements($tags,3);

        //données statique
        $datas= [
                    ['name' => 'Site vitine',
                    'description' => null,
                    'client_name' => 'emma',
                    'start_date' => new DateTime('2022-05-01'),
                    'checkpoint_date'=> new DateTime('2022-07-01'),
                    'delivery_date'=> new DateTime('2022-11-01'),
                    'tags' => [$htmlTag, $cssTag],
                    ],  
                    [
                    'name' => 'Wordpress',
                    'description' => null,
                    'client_name' => 'justine',
                    'start_date' => new DateTime('2022-05-01'),    
                    'checkpoint_date' =>new DateTime('2022-07-01'),
                    'delivery_date' =>new DateTime('2022-11-01'),
                    'tags' => [$htmlTag, $jsTag],

                    ],

                    ['name' => 'API Rest',
                    'description' => null,
                    'client_name'=> 'alex',
                    'start_date'=> new DateTime('2022-05-01'),
                    'checkpoint_date'=>new DateTime('2022-07-01'),
                    'delivery_date'=>new DateTime('2022-11-01'),
                    'tags' => [$jsTag],
                    ],
                
                ];
        
                foreach ($datas as $data){   //à gauche la liste à droite un des élement de la liste 
            $project = new Project();
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClientName($data['client_name']);
            $project->setStartDate($data['start_date']);
            $project->setCheckpointDate($data['checkpoint_date']);
            $project->setDeliveryDate($data['delivery_date']);

            foreach ($data['tags'] as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project); 

            }
            $this->manager->flush();

            //données dynamique
            for ($i = 0; $i < 10; $i++){
                $project = new Project();
                
                $words = random_int(3 , 6); // $words est une variable de données aléatoire
                $project->setName($this->faker->sentence($words));

                $words = random_int(8 , 10);
                $project->setDescription($this->faker->optional(0.7)->sentence($words)); //optionel dans 70% des cas

                $words = random_int(2 , 8);
                $project->setClientName($this->faker->unique()->sentence($words));

                $startDate = $this->faker->dateTimeBetween('-1 year', '-11 months');
                $project->setStartDate($startDate);

                $checkPointDate = $this->faker->dateTimeBetween('-11 months', '-8 months');
                $project->setCheckpointDate($checkPointDate);

                $deliveryDate = $this->faker->dateTimeBetween('-8 months', '-2 months');
                $project->setDeliveryDate($deliveryDate);

                //on choisit le nombre de tag au hasard entre 1 et 4
                $tagsCount = random_int(1 , 4);
                //on choisit des tags au hasard depuis la liste compléte
                $shortList = $this->faker->randomElements($tags, $tagsCount);

                //on passe en revue chaque tag de la short liste
                foreach ($shortList as $tag){
                    //on associe un tag avec le projet
                    $project->addTag($tag);
                }

                $this->manager->persist($project);
            }

            $this->manager->flush();
    }

  }

