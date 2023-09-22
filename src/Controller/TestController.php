<?php

namespace App\Controller;

use Exception;
use App\Entity\Tag;
use App\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\SchoolYear;
use datetime;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/tag', name: 'app_test_tag')]
    public function tag(ManagerRegistry $doctrine): Response
    {   
        $em = $doctrine->getManager();
        $tagRepository = $em->getRepository(Tag :: class);

        $studentRepository = $em->getRepository(Student :: class);

        //création d'un objet
        $foo = new Tag();
        $foo ->setName('foo');
        $foo->setDescription('Foo Bar Baz');
        $em->persist($foo);

        try{
            $em->flush();
        } catch(Exception $e){
            // gérer l'erreur
            dump($e->getMessage());
        }

        //récuperation du student dont l'id est 1
        $student = $studentRepository->find(1);

        // récuperation du tag dont l'id est 4
        $tag4 = $tagRepository->find(4);

        // association du tag 4 au student1
        $student->addTag($tag4);
        $em->flush();

        // récuperation de la liste compléte des objets
        $tags = $tagRepository->findAll();

        // récuperation de l'objet dont l'id est 1
        $tag = $tagRepository->find(1);

        // récuperation de l'objet dont l'id est 14
        $tag15 = $tagRepository->find(15);

        // suppression de l'objet seulement si il existe
        if ($tag15){
            // suppression de l'objet
            $em->remove($tag15);
            $em->flush();
        }

        // récuperation de l'objet dont l'id est 4
        $tag4 = $tagRepository->find(4);

        // modification d'un objet
        $tag4->setName('Python');
        $tag4->setDescription(null);
        // pas la peine d'appeller persist() si l'objet provient de la BDD
        $em->flush();

        //récuperation d'un tag dont le nom est css
        $cssTag = $tagRepository->findOneBy([
            //critéres de recherche
            'name' => 'foo',
         ]); 
            // critéres de tri
            //'name' => 'ASC',        // ASC = asecendant  DESC = descandant
            //]);

        //récuperation de tout les tags dont la description est nulle
        $nullDescriptionTags = $tagRepository->findBy([
            //critéres de recherche
            'description' => null,
        ], [
            // critéres de tri
            'name' => 'ASC',
        ]); 
        // ou
        $nullDescriptionTags = $tagRepository->findByNotNullDescription();

        // récuperation de tous les tags avec description
        $notNullDescriptionTags = $tagRepository->findByNotNullDescription();

        // récuperation des tags qui contiennent certains mot-clés
        $keywordTags1 = $tagRepository->findByKeyword('HTML');

        // récuperation de tag a partir d'une schoolyear
        $schoolYearRepository = $em->getRepository(schoolYear :: class);
        $schoolYear = $schoolYearRepository->find(1);
        $schoolYearTags = $tagRepository->findBySchoolYear($schoolYear);

        // mise à jour des relaions d'un tag
        $studentRepository = $em->getRepository(student::class);
        $student = $studentRepository->find(2);
        $htmlTag = $tagRepository->find(1);
        $htmlTag->addStudent($student);
        $em->flush();
    

         // récuperation de la liste compléte des objets
         $tags = $tagRepository->findAll();
       
        $title = 'Test des tags';

        return $this->render('test/tag.html.twig', [
            'title' => $title,
            'tags' => $tags,
            'tag' =>$tag,
            'cssTag'=> $cssTag,
            'nullDescriptionTags' => $nullDescriptionTags,
            'notNullDescriptionTags' => $notNullDescriptionTags,
            'keywordTags1' => $keywordTags1,
            'schoolYearTags' => $schoolYearTags,
            'htmlTag' => $htmlTag,
        ]);
    }

    #[Route('/school-year', name: 'app_test_schoolyear')]
    public function schoolyear(ManagerRegistry $doctrine) : Response {

        $em = $doctrine->getManager();
        $schoolRepository = $em->getRepository(SchoolYear :: class);

        $school = new schoolYear();
        $school ->setName('P11');
        $school->setDescription('Promo P11');
        $school->setstartDate (new datetime('01-01-23'));
        $school->setendDate (new datetime('10-02-23'));
        $em->persist($school);
        $em->flush();

        $schoolYears = $schoolRepository->findAll();
        $schoolYear = $schoolRepository->find(1);
        $title = 'Liste des schoolYear';

        
        return $this->render('test/school-year.html.twig',[

            'title' => $title,
            'schoolYears' => $schoolYears,
            'schoolYear'=> $schoolYear,

        ]);
    }
}
