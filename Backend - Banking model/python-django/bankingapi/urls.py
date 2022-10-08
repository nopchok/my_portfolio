from django.urls import path
from bankingapi.views import *


urlpatterns=[
    path('api/get_current_balance', select.getCurrentBalance, name='getCurrentBalance'),
    path('api/get_transaction_history', select.getTransactionHistory, name='getTransactionHistory'),
    path('api/new_bank_account', create.newBankAccount, name='newBankAccount'),
    path('api/transfer_amount', create.transferAmount, name='transferAmount'),
]