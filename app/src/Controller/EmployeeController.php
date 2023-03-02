<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Employee;
use App\Repository\CategoryRepository;
use App\Repository\CitizenshipRepository;
use App\Repository\CityRepository;
use App\Repository\ConscriptRepository;
use App\Repository\DisabilityRepository;
use App\Repository\EmployeeRepository;
use App\Repository\FamilyStatusRepository;
use App\Repository\UserRepository;
use App\Validation\CategoryValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\Framework\Assets\PackageConfig;

/**
 * Class PostController
 * @package App\Controller
 * @Route("/employee", name="post_api")
 */
class EmployeeController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("/list", name="list", methods={"GET"})
     */
    public function getListEmployee(Request $request, EmployeeRepository $repository)
    {
        $sort = $request->query->get('sort') ?? [];
        $nextSort = 'ASC';
        switch ($sort) {
            case 'ASC':
                $nextSort = "DESC";
                break;
            case 'DESC':
                $nextSort = null;
                break;
            case null:
                $nextSort = "ASC";
                break;
            default:
                $sort = null;
                break;
        }
        if ($sort) {
            $data = $this->transformData($repository->findBy([], ['lastName' => $sort]));
        } else {
            $sort = 'ASC';
            $data = $this->transformData($repository->findAll());
        }
        return $this->render('employees.html.twig', [
            'employees' => $data,
            'sort' => $nextSort
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/view/{employee}", name="view_one_employee", methods={"GET"})
     */
    public function viewEmployee(
        Employee $employee,
        DisabilityRepository   $disabilityRepository,
        CityRepository         $cityRepository,
        CitizenshipRepository  $citizenshipRepository,
        FamilyStatusRepository $familyStatusRepository,
        ConscriptRepository    $conscriptRepository
    )
    {
        $city = [];
        $disability = [];
        $citizenship = [];
        $familyStatus = [];
        $conscript = [];
        foreach ($cityRepository->findAll() as $item) {
            $city[] = $item->getName();
        }
        foreach ($disabilityRepository->findAll() as $item) {
            $disability[] = $item->getName();
        }
        foreach ($citizenshipRepository->findAll() as $item) {
            $citizenship[] = $item->getName();
        }
        foreach ($familyStatusRepository->findAll() as $item) {
            $familyStatus[] = $item->getName();
        }
        foreach ($conscriptRepository->findAll() as $item) {
            $conscript[] = $item->getName();
        }
        return $this->render('editForm.html.twig', [
            'city' => $city,
            'disability' => $disability,
            'citizenship' => $citizenship,
            'familyStatus' => $familyStatus,
            'conscript' => $conscript,
            'employee' => [
                'fname'=>$employee->getFirstName(),
                'lname'=>$employee->getLastName(),
                'pname'=>$employee->getPatronicName(),
                'id'=>$employee->getId(),
                'placebirth'=>$employee->getPlaceOfBirth(),
                'address'=>$employee->getAdress(),
                'phone'=>$employee->getPhoneHome(),
                'mobilephone'=>$employee->getPhoneMobile(),
                'mail' => $employee->getMail(),
                'workingplace' => $employee->getWorkingPlace(),
                'passportseries' => $employee->getPassportSeries(),
                'passportnumber' => $employee->getPassportNumber(),
                'passportissuedby' => $employee->getPassportIssuedBy(),
                'position' => $employee->getPosition(),
                'income' => $employee->getIncome(),
            ]
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/remove/{employee}", name="remove_employee", methods={"POST"})
     */
    public function deleteEmployee(Employee $employee, EntityManagerInterface $em)
    {
        $em->remove($employee);
        $em->flush();
        return $this->redirect('/employee/list');
    }


    /**
     * @return JsonResponse
     * @Route("/add", name="add_employee", methods={"GET"})
     */
    public function addEmployee(
        DisabilityRepository   $disabilityRepository,
        CityRepository         $cityRepository,
        CitizenshipRepository  $citizenshipRepository,
        FamilyStatusRepository $familyStatusRepository,
        ConscriptRepository    $conscriptRepository
    )
    {
        $city = [];
        $disability = [];
        $citizenship = [];
        $familyStatus = [];
        $conscript = [];
        foreach ($cityRepository->findAll() as $item) {
            $city[] = $item->getName();
        }
        foreach ($disabilityRepository->findAll() as $item) {
            $disability[] = $item->getName();
        }
        foreach ($citizenshipRepository->findAll() as $item) {
            $citizenship[] = $item->getName();
        }
        foreach ($familyStatusRepository->findAll() as $item) {
            $familyStatus[] = $item->getName();
        }
        foreach ($conscriptRepository->findAll() as $item) {
            $conscript[] = $item->getName();
        }
        return $this->render('addForm.html.twig', [
            'city' => $city,
            'disability' => $disability,
            'citizenship' => $citizenship,
            'familyStatus' => $familyStatus,
            'conscript' => $conscript
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/add", name="add_post_employee", methods={"POST"})
     */
    public function addEmployeePost(
        Request                $request,
        DisabilityRepository   $disabilityRepository,
        CityRepository         $cityRepository,
        CitizenshipRepository  $citizenshipRepository,
        FamilyStatusRepository $familyStatusRepository,
        ConscriptRepository    $conscriptRepository,
        EntityManagerInterface $entityManager
    )
    {
        $data = ($request->request->all());
        $error = $this->transformPostData($request->request->all());
        if ($error) {
            return $this->render('error.html.twig', [
                'error' => $error
            ]);
        }
        $employee = new Employee();
        $employee->setFirstName($data['fname']);
        $employee->setLastName($data['lname']);
        $employee->setPatronicName($data['pname']);
        $employee->setDateOfBirth(new \DateTime($data['birthdate']));
        $employee->setCity($cityRepository->findOneBy(['name' => $data['city']]));
        $employee->setPlaceOfBirth($data['placebirth']);
        $employee->setAdress($data['address']);
        $employee->setFloor($data['floor'] == 'M');
        $employee->setIsPensioner($data['floor'] == 'Y');
        $employee->setPhoneMobile($data['mobilephone']);
        $employee->setPhoneHome($data['phone']);
        $employee->setMail($data['mail']);
        $employee->setWorkingPlace($data['workingplace']);
        $employee->setPosition($data['position']);
        $employee->setDisability($disabilityRepository->findOneBy(['name' => $data['disability']]));
        $employee->setCitizenship($citizenshipRepository->findOneBy(['name' => $data['citizenship']]));
        $employee->setFamilyStatus($familyStatusRepository->findOneBy(['name' => $data['familyStatus']]));
        $employee->setConscript($conscriptRepository->findOneBy(['name' => $data['conscript']]));
        $employee->setPassportCity($cityRepository->findOneBy(['name' => $data['passportcity']]));
        $employee->setPassportSeries($data['passportseries']);
        $employee->setPassportNumber($data['passportnumber']);
        $employee->setPassportStartDate(new \DateTime($data['passportstartdate']));
        $employee->setPassportIssuedBy($data['passportissuedby']);
        $employee->setPassportIssuedBy($data['passportissuedby']);
        $employee->setIncome($data['income']);
        $entityManager->persist($employee);
        $entityManager->flush();

        return $this->redirect('/employee/list');
    }

    /**
     * @return JsonResponse
     * @Route("/view/{employee}", name="update_post_employee", methods={"POST"})
     */
    public function updateEmployeePost(
        Employee $employee,
        Request                $request,
        DisabilityRepository   $disabilityRepository,
        CityRepository         $cityRepository,
        CitizenshipRepository  $citizenshipRepository,
        FamilyStatusRepository $familyStatusRepository,
        ConscriptRepository    $conscriptRepository,
        EntityManagerInterface $entityManager
    )
    {
        $data = ($request->request->all());
        $error = $this->transformPostData($request->request->all());
        if ($error) {
            return $this->render('error.html.twig', [
                'error' => $error
            ]);
        }
        $employee->setFirstName($data['fname']);
        $employee->setLast($data['lname']);
        $employee->setPatronicName($data['pname']);
        $employee->setDateOfBirth(new \DateTime($data['birthdate']));
        $employee->setCity($cityRepository->findOneBy(['name' => $data['city']]));
        $employee->setPlaceOfBirth($data['placebirth']);
        $employee->setAdress($data['address']);
        $employee->setFloor($data['floor'] == 'M');
        $employee->setIsPensioner($data['floor'] == 'Y');
        $employee->setPhoneMobile($data['mobilephone']);
        $employee->setPhoneHome($data['phone']);
        $employee->setMail($data['mail']);
        $employee->setWorkingPlace($data['workingplace']);
        $employee->setPosition($data['position']);
        $employee->setDisability($disabilityRepository->findOneBy(['name' => $data['disability']]));
        $employee->setCitizenship($citizenshipRepository->findOneBy(['name' => $data['citizenship']]));
        $employee->setFamilyStatus($familyStatusRepository->findOneBy(['name' => $data['familyStatus']]));
        $employee->setConscript($conscriptRepository->findOneBy(['name' => $data['conscript']]));
        $employee->setPassportCity($cityRepository->findOneBy(['name' => $data['passportcity']]));
        $employee->setPassportSeries($data['passportseries']);
        $employee->setPassportNumber($data['passportnumber']);
        $employee->setPassportStartDate(new \DateTime($data['passportstartdate']));
        $employee->setPassportIssuedBy($data['passportissuedby']);
        $employee->setIncome($data['income']);
        $entityManager->persist($employee);
        $entityManager->flush();

        return $this->redirect('/employee/list');
    }

    private function transformPostData($request)
    {
        $error = null;
        foreach ($request as $item) {
            if (!$item || $item == '') {
                $error = 'Eсть пустые поля';
            }
        }
        if (strlen($request['passportnumber']) != 2) {
            $error = 'Ошибка номера паспорта';
        }
        return $error;
    }

    private function transformData(?array $data)
    {
        $result = [];
        /** @var Employee $item */
        foreach ($data as $item) {
            $result[] = [
                'fullName' => $item->getLastName(),
                'date' => $item->getDateOfBirth()->format('Y-m-d'),
                'id' => $item->getId()
            ];
        }
        return $result;
    }
}