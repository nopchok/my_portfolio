from django.test import TestCase, Client
from django.urls import reverse
from bankingapi.models import Customer, BankAccount, TransactionHistory

from decimal import Decimal
import json


class TestModel(TestCase):

    def testCustomer(self):
        Customer.objects.create(Name="Mr. JoJo")
        JoJo = Customer.objects.filter(Name="Mr. JoJo")
        self.assertEqual(True, JoJo.exists())

    def testBankAccount(self):
        JoJo = Customer.objects.create(Name="Mr. JoJo")
        JoJoAcc = BankAccount.objects.create(CustomerId=JoJo, CurrentBalance=500)
        self.assertEqual(Decimal('500.00'), BankAccount.objects.get(Id=JoJoAcc.Id).CurrentBalance)

    def testTransactionHistory(self):
        JoJo = Customer.objects.create(Name="Mr. JoJo")
        JoJoAcc = BankAccount.objects.create(CustomerId=JoJo, CurrentBalance=500)
        Trans = TransactionHistory.objects.create(Type="withdraw", BankAccountId=JoJoAcc, Amount=10)
        self.assertEqual(Decimal('10.00'), TransactionHistory.objects.get(Id=Trans.Id).Amount)


