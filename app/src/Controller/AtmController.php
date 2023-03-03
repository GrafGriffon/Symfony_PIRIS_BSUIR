<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Employee;
use App\Repository\AccountRepository;
use App\Repository\CardRepository;
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
 * @Route("/atm", name="post_api")
 */
class AtmController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Route("", name="main_atm_page_get", methods={"GET"})
     */
    public function getPageATM(Request $request, EmployeeRepository $repository)
    {
        session_start();
        if(key_exists('card', $_SESSION)){
            return $this->render('operationsAtm.html.twig', []);
        }
        return $this->render('mainAtm.html.twig', []);
    }

    /**
     * @return JsonResponse
     * @Route("/exit", name="exit_atm_page_get", methods={"GET"})
     */
    public function getExitPageATM()
    {
        session_start();
        unset($_SESSION['card']);
        return $this->redirect('/atm');
    }

    /**
     * @return JsonResponse
     * @Route("/balance", name="balance_atm_page_get", methods={"GET"})
     */
    public function getBalancePageATM(CardRepository $repository)
    {
        session_start();
        $card = $repository->find($_SESSION['card']);
        return $this->render('balanceAtm.html.twig',
        [
            'employee' => $card->getAccount()->getEmployee()->getFullName(),
            'balance' => $card->getAccount()->getCount(),
            'currency' => $card->getAccount()->getCurrency()->getName()
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/money", name="money_atm_page_get", methods={"GET"})
     */
    public function getMoneyPageATM(CardRepository $repository)
    {
        return $this->render('getMoneyFromCard.html.twig', []);
    }

    /**
     * @return JsonResponse
     * @Route("/money", name="money_atm_page_post", methods={"POST"})
     */
    public function getMoneyPageATMPOST(CardRepository $repository, Request $request, EntityManagerInterface $entityManager)
    {
        $data = $request->request->all();
        session_start();
        $card = $repository->find($_SESSION['card']);
        if ($card->getPassword() == $data['password']){
            $card->getAccount()->setCount($card->getAccount()->getCount()-$data['count']);
            $entityManager->flush();
        }
            return $this->redirect('/atm');
    }

    /**
     * @Route("", name="main_atm_page_post", methods={"POST"})
     */
    public function getPageATMPOST(Request $request, CardRepository $repository)
    {
        $data = $request->request->all();
        if ($card = $repository->findOneBy(['code' => $data['number'], 'password' => $data['password']])){
            session_start();
            $_SESSION["card"] = $card->getId();
        }
        return $this->redirect('/atm');
    }
}