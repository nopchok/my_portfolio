from django.test import TestCase, Client
from django.urls import reverse
from bankingapi.models import Customer, BankAccount, TransactionHistory

from decimal import Decimal
import json

from bankingapi.views.common import ERROR_MESSAGE



class TestClassView(TestCase):
    def setUp(self):
        self.client = Client()
    
    def client_post(self, url, data):
        return self.client.post(url, data, content_type='application/json')
    
    def test404(self):
        response = self.client.get('/test_404')
        self.assertEqual(404, response.status_code)
        
class TestClassViewGetBalance(TestCase):
    def setUp(self):
        self.client = Client()
        self.url = '/c_api/get_current_balance'
    
    def client_post(self, url, data):
        return self.client.post(url, data, content_type='application/json')
    
    def test404(self):
        response = self.client.get('/test_404')
        self.assertEqual(404, response.status_code)
    
    def testGetBalance(self):
        response = self.client.get(self.url)
        self.assertEqual(False, response.json()['success'])
    
    def testGetBalancePost(self):
        JoJo = Customer.objects.create(Name="Mr. JoJo")
        JoJoAcc = BankAccount.objects.create(CustomerId=JoJo, CurrentBalance=500)

        response = self.client_post(self.url, {'bank_account_id': 1})
        self.assertEqual(True, response.json()['success'])

class TestGetBalance(TestCase):
    def setUp(self):
        self.client = Client()
    
    def client_post(self, url, data):
        return self.client.post(url, data, content_type='application/json')

    def testGetBalance(self):
        JoJo = Customer.objects.create(Name="Mr. JoJo")
        JoJoAcc = BankAccount.objects.create(CustomerId=JoJo, CurrentBalance=500)

        response = self.client_post(reverse('getCurrentBalance'), {'bank_account_id': 1})
        self.assertEqual('500.00', response.json()['data'])
        
    def testGetBalanceNoneAcc(self):
        response = self.client_post(reverse('getCurrentBalance'), {'bank_account_id': 999})
        self.assertEqual(ERROR_MESSAGE.bankAccountNotFound, response.json()['message'])
        
    def testGetBalanceFalseParam(self):
        response = self.client_post(reverse('getCurrentBalance'), {'id': 1})
        self.assertEqual(ERROR_MESSAGE.parameterError, response.json()['message'])




class TestGetTransactionHistory(TestCase):
    def setUp(self):
        self.client = Client()

        self.client_post(reverse('newBankAccount'), {'name': 'Mr. JoJo', 'initial_deposit': 500})
        self.client_post(reverse('newBankAccount'), {'name': 'Mr. JoJo', 'initial_deposit': 500})
        
        self.Acc1 = BankAccount.objects.get(Id=1)
        self.Acc2 = BankAccount.objects.get(Id=2)
    
    def client_post(self, url, data):
        return self.client.post(url, data, content_type='application/json')

    def testGetHistory(self):
        self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 2, 'amount': 100})
        self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 2, 'amount': 100})
        self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 2, 'amount': 100})

        response = self.client_post(reverse('getTransactionHistory'), {'bank_account_id': 1})
        # include initial_deposit
        self.assertEqual(4, len(response.json()['data']) )
        
    def testGetHistoryNoneAcc(self):
        response = self.client_post(reverse('getTransactionHistory'), {'bank_account_id': 999})
        self.assertEqual(ERROR_MESSAGE.bankAccountNotFound, response.json()['message'])
        
    def testGetHistoryFalseParam(self):
        response = self.client_post(reverse('getTransactionHistory'), {'id': 1})
        self.assertEqual(ERROR_MESSAGE.parameterError, response.json()['message'])





class TestNewBankAccount(TestCase):
    def setUp(self):
        self.client = Client()
    
    def client_post(self, url, data):
        return self.client.post(url, data, content_type='application/json')

    def testCustomerFalseParam(self):
        response = self.client_post(reverse('newBankAccount'), {'name': 'Mr. JoJo', 'amount': 500})
        self.assertEqual(ERROR_MESSAGE.parameterError, response.json()['message'])

    def testMultipleAcc(self):
        customer_name = 'Mr. JoJo'
        response1 = self.client_post(reverse('newBankAccount'), {'name': customer_name, 'initial_deposit': 500})
        response2 = self.client_post(reverse('newBankAccount'), {'name': customer_name, 'initial_deposit': 500})

        Acc1 = BankAccount.objects.get(Id=1)
        Acc2 = BankAccount.objects.get(Id=2)

        self.assertEqual(Acc1.CurrentBalance, Acc2.CurrentBalance)
        

class TestTransferAmount(TestCase):
    def setUp(self):
        self.client = Client()
        
        self.client_post(reverse('newBankAccount'), {'name': 'Mr. JoJo', 'initial_deposit': 500})
        self.client_post(reverse('newBankAccount'), {'name': 'Mr. JoJo', 'initial_deposit': 500})
        self.client_post(reverse('newBankAccount'), {'name': 'Mr. LiLi', 'initial_deposit': 500})

        self.Acc1 = BankAccount.objects.get(Id=1)
        self.Acc2 = BankAccount.objects.get(Id=2)
        self.Acc3 = BankAccount.objects.get(Id=3)
    
    def client_post(self, url, data):
        return self.client.post(url, data, content_type='application/json')

    def testTransferAmountFalseParam(self):
        response = self.client_post(reverse('transferAmount'), {'from': 1, 'to': 2, 'amount': 500})
        self.assertEqual(ERROR_MESSAGE.parameterError, response.json()['message'])

    def testTransferAmountSameAcc(self):
        response = self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 1, 'amount': 500})
        self.assertEqual(ERROR_MESSAGE.sameAccount, response.json()['message'])

    def testTransferAmountSameCustomer(self):
        response = self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 2, 'amount': 500})

        response = self.client_post(reverse('getCurrentBalance'), {'bank_account_id': 2})
        self.assertEqual('1000.00', response.json()['data'])
        response = self.client_post(reverse('getCurrentBalance'), {'bank_account_id': 1})
        self.assertEqual('0.00', response.json()['data'])

    def testTransferAmountDiffCustomer(self):
        response = self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 3, 'amount': 500})

        response = self.client_post(reverse('getCurrentBalance'), {'bank_account_id': 3})
        self.assertEqual('1000.00', response.json()['data'])
        response = self.client_post(reverse('getCurrentBalance'), {'bank_account_id': 1})
        self.assertEqual('0.00', response.json()['data'])

    def testTransferOver(self):
        response = self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 2, 'amount': 5000})
        self.assertEqual(ERROR_MESSAGE.balanceNotEnough, response.json()['message'])
        
    def testTransferNoneAcc(self):
        response = self.client_post(reverse('transferAmount'), {'from_bank_account_id': 1, 'to_bank_account_id': 20, 'amount': 500})
        self.assertEqual(ERROR_MESSAGE.bankAccountNotFound, response.json()['message'])
