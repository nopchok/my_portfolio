from django.db import models

# Create your models here.


class Customer(models.Model):
    Id = models.AutoField(primary_key=True)
    Name = models.CharField(max_length=255)
    DateCreate = models.DateTimeField(auto_now_add=True)

class BankAccount(models.Model):
    Id = models.AutoField(primary_key=True)
    CustomerId = models.ForeignKey(Customer, null=True, on_delete=models.SET_NULL)
    CurrentBalance = models.DecimalField(max_digits=20, decimal_places=2)
    DateCreate = models.DateTimeField(auto_now_add=True)

class TransactionHistory(models.Model):
    Id = models.AutoField(primary_key=True)
    Type = models.CharField(max_length=255)
    BankAccountId = models.ForeignKey(BankAccount, null=True, on_delete=models.SET_NULL, related_name = 'BankAccountId')
    Amount = models.DecimalField(max_digits=20, decimal_places=2)
    RelateBankAccountId = models.ForeignKey(BankAccount, null=True, on_delete=models.SET_NULL, related_name = 'RelateBankAccountId')
    DateCreate = models.DateTimeField(auto_now_add=True)