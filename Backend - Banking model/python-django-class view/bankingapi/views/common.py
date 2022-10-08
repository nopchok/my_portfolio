
from django.http.response import JsonResponse


from bankingapi.models import Customer, BankAccount, TransactionHistory

import json
import sys
from decimal import Decimal




class ERROR_MESSAGE():
    bankAccountNotFound = 'Bank account is not found'
    somethingWentWrong = 'Something went wrong'
    parameterError = 'Missing/Error Parameter'
    amountZero = 'Amount must be greater than 0'
    sameAccount = 'You cannot transfer to same bank account'
    balanceNotEnough = 'Current balance is not enough'
    actionNotFound = 'Action not found'
    methodNotFound = 'Method not found'
    requestDataError = 'Request data error'
    headerError = 'Header error'
    

def get_balance(id):
    try:
        qs = BankAccount.objects.get(Id=id)
        return Decimal(qs.CurrentBalance)
    except:
        return None