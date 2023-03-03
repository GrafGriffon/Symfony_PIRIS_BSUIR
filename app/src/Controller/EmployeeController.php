<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Employee;
use App\Repository\AccountRepository;
use App\Repository\CategoryRepository;
use App\Repository\CitizenshipRepository;
use App\Repository\CityRepository;
use App\Repository\ConscriptRepository;
use App\Repository\CurrencyRepository;
use App\Repository\DisabilityRepository;
use App\Repository\EmployeeRepository;
use App\Repository\FamilyStatusRepository;
use App\Repository\TypeCreditRepository;
use App\Repository\TypeDepositRepository;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/list", name="end_bank_day", methods={"POST"})
     */
    public function endBankDay(Request $request, AccountRepository $accountRepository, EntityManagerInterface $entityManager)
    {
        foreach ($accountRepository->findAll() as $account) {
            if ($account->getEndDateDeposit() && $account->getEndDateDeposit() > (new \DateTime())) {
                if ($account->getTypeDeposit()){
                    $account->setCountPercent(($account->getCount() + $account->getCountPercent()) * (1 + $account->getTypeDeposit()->getPercent() / 1000) - $account->getCount());
                }
                elseif ($account->getTypeCredit()){
                    $accountEmployee = $accountRepository->findOneBy(['employee' => $account->getEmployee(), 'startDateDeposit'=>null]);
                    $countMonths = date_diff(date_create($account->getStartDateDeposit()->format('Y-m-d')), date_create($account->getEndDateDeposit()->format('Y-m-d')))->format('%m');
                    $accountEmployee->setCount($accountEmployee->getCount() + $account->getCount()*$account->getTypeCredit()->getPercent()/100/30/$countMonths);
                    $account->setCountPercent(($account->getCount() - $account->getCount()*$account->getTypeCredit()->getPercent()/100/30/$countMonths + $account->getCountPercent()) * (1 + $account->getTypeCredit()->getPercent() / 100) - $account->getCount());
                }
            }
        }
        $entityManager->flush();

        return $this->redirect('/employee/list');
    }

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
     * @Route("/view/{employee}", name="view_one_employee_1", methods={"GET"})
     */
    public function viewEmployee(
        Employee               $employee,
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
                'fname' => $employee->getFirstName(),
                'lname' => $employee->getLastName(),
                'pname' => $employee->getPatronicName(),
                'id' => $employee->getId(),
                'placebirth' => $employee->getPlaceOfBirth(),
                'address' => $employee->getAdress(),
                'phone' => $employee->getPhoneHome(),
                'mobilephone' => $employee->getPhoneMobile(),
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/view/{employee}/operations", name="view_one_employee", methods={"GET"})
     */
    public function viewEmployeeOperations(
        Employee          $employee,
        AccountRepository $accountRepository
    )
    {
        $accounts = [];
        foreach ($accountRepository->findBy(['employee' => $employee]) as $account) {
            $pressed = false;
            if ($account->getTypeDeposit()) {
                $pressed = $account->getTypeDeposit()->getIsReturnable() == false;
            }
            if ($pressed) {
                $pressed = $account->getEndDateDeposit() ?? false;
            }
            $local = ($account->getTypeCredit() ? $account->getTypeCredit()->getName() : 'Основной счет');
            $accounts[] = [
                'count' => $account->getCount() / $account->getCurrency()->getIndex(),
                'currency' => $account->getCurrency()->getName(),
                'number' => $account->getNumber(),
                'type' => $account->getTypeDeposit() ? $account->getTypeDeposit()->getName() : $local,
                'start' => $account->getStartDateDeposit() ? $account->getStartDateDeposit()->format('d-m-y') : null,
                'end' => $account->getEndDateDeposit() ? $account->getEndDateDeposit()->format('d-m-y') : null,
                'percent' => $account->getTypeDeposit() ? $account->getTypeDeposit()->getPercent() : null,
                'pressed' => $pressed && ($pressed < (new \DateTime()) ? true : null),
//              'pressed' => true,
                'id' => $account->getId()
            ];
        }
        return $this->render('operations.html.twig', [
            'employee' => $employee->getId(),
            'currBalance' => $accountRepository->findOneBy(['employee' => $employee])->getCount(),
            'deposBalance' => $accountRepository->findOneBy(['employee' => $employee])->getCount(),
            'accounts' => $accounts
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/view/{employee}/operations/{account}", name="get_miney_deposit", methods={"POST"})
     */
    public function setMoneyFromDepositEmployee(Employee $employee, Account $account, EntityManagerInterface $em, AccountRepository $accountRepository)
    {
        $mainAccountBank = $accountRepository->findOneBy(['employee' => null]);
        $mainAccount = $accountRepository->findOneBy(['employee' => $employee, 'startDateDeposit' => null]);
        $mainAccount->setCount($mainAccount->getCount() + $account->getCount() + $account->getCountPercent());
        $em->remove($account);
        $em->flush();
        return $this->redirect('/employee/list');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/remove/{employee}", name="remove_employee", methods={"POST"})
     */
    public function deleteEmployee(Employee $employee, EntityManagerInterface $em)
    {
        $em->remove($employee);
        $em->flush();
        return $this->redirect('/employee/list');
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{employee}/add/depos", name="add_depos_employee1", methods={"GET"})
     */
    public function addEmployeeDepos(
        Employee              $employee,
        TypeDepositRepository $typeDepositRepository
    )
    {
        $types = [];
        foreach ($typeDepositRepository->findAll() as $item) {
            $types[] = $item->getName() . ' ' . $item->getPercent() . '%';
        }
        return $this->render('addDepos.html.twig', [
            'types' => $types,
            'employee' => $employee->getId(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{employee}/add/credit", name="add_credit_employee1", methods={"GET"})
     */
    public function addEmployeeCredit(
        Employee              $employee,
        TypeCreditRepository $typeCreditRepository
    )
    {
        $types = [];
        foreach ($typeCreditRepository->findAll() as $item) {
            $types[] = $item->getName() . ' ' . $item->getPercent() . '%';
        }
        return $this->render('addCredit.html.twig', [
            'types' => $types,
            'employee' => $employee->getId(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{employee}/add/depos", name="add_depos_employee2", methods={"POST"})
     */
    public function addPostEmployeeDepos(
        Request               $request,
        Employee              $employee,
        CurrencyRepository $currencyRepository,
        EntityManagerInterface $entityManager,
        AccountRepository $accountRepository,
        TypeDepositRepository $typeDepositRepository
    )
    {
        $data = ($request->request->all());
        $account = (new Account());
        $account->setCount($data['count']);
        $account->setCountPercent(0);
        $account->setCurrency($currencyRepository->find(1));
        $account->setEndDateDeposit(new \DateTime($data['end']));
        $account->setStartDateDeposit(new \DateTime($data['start']));
        $account->setNumber(random_int(0, 1000));
        $account->setEmployee($employee);
        $mainAccount = $accountRepository->findOneBy(['employee' => $employee, 'startDateDeposit'=>null]);
        $mainAccount->setCount($mainAccount->getCount()-$data['count']);
        $types = [];
        foreach ($typeDepositRepository->findAll() as $item) {
            if(($item->getName() . ' ' . $item->getPercent() . '%') == $data['type']){
                $account->setTypeDeposit($item);
                break;
            }
        }
        $entityManager->persist($account);
        $entityManager->flush();
        return $this->render('addDepos.html.twig', [
            'types' => $types,
            'employee' => $employee->getId(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/{employee}/add/credit", name="add_crediit_employee2", methods={"POST"})
     */
    public function addPostEmployeeCredit(
        Request               $request,
        Employee              $employee,
        CurrencyRepository $currencyRepository,
        EntityManagerInterface $entityManager,
        AccountRepository $accountRepository,
        TypeCreditRepository $typeCreditRepository
    )
    {
        $data = ($request->request->all());
        $account = (new Account());
        $account->setCount(-$data['count']);
        $account->setCountPercent(0);
        $account->setCurrency($currencyRepository->find(1));
        $account->setEndDateDeposit((new \DateTime($data['start']))->add(new \DateInterval('P'.$data['countmonths'].'M')));
        $account->setStartDateDeposit(new \DateTime($data['start']));
        $account->setNumber(random_int(0, 1000));
        $account->setEmployee($employee);
        $mainAccount = $accountRepository->findOneBy(['employee' => $employee, 'startDateDeposit'=>null]);
        $mainAccount->setCount($mainAccount->getCount()+$data['count']);
        $types = [];
        foreach ($typeCreditRepository->findAll() as $item) {
            if(($item->getName()) == $data['type']){
                $account->setTypeCredit($item);
                break;
            }
        }
        $entityManager->persist($account);
        $entityManager->flush();
        return $this->render('addDepos.html.twig', [
            'types' => $types,
            'employee' => $employee->getId(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
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
        $employee->setFloor($data['floor'] === 'M');
        $employee->setIsPensioner($data['floor'] === 'Y');
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
        Employee               $employee,
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
                'fullName' => $item->getFullName(),
                'date' => $item->getDateOfBirth()->format('Y-m-d'),
                'id' => $item->getId()
            ];
        }
        return $result;
    }
}