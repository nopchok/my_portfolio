from bankingapi.views.common import *



@csrf_exempt
def getTransactionHistory(request):
    data = {
        'success': False
    }
    if request.method == 'POST':
        id = json.loads(request.body).get('bank_account_id')
        if id is None:
            data['message'] = ERROR_MESSAGE.parameterError
        else:
            try:
                BankAccount.objects.get(Id=id)
            except:
                data['message'] = ERROR_MESSAGE.bankAccountNotFound
            
            try:
                if data.get('message') is None:
                    qs = TransactionHistory.objects.filter(BankAccountId=id)

                    RelateCustomerName = [x.RelateBankAccountId.CustomerId.Name if x.RelateBankAccountId is not None else None for x in qs]
                    data = {
                        'success': True,
                        'data': [dict(x, **{'RelateCustomerName': RelateCustomerName[i]}) for i,x in enumerate(list(qs.values()))]
                    }
            except:
                print(sys.exc_info())
                data['message'] = ERROR_MESSAGE.somethingWentWrong

    return JsonResponse(data)



@csrf_exempt
def getCurrentBalance(request):
    data = {
        'success': False
    }
    if request.method == 'POST':
        id = json.loads(request.body).get('bank_account_id')
        if id is None:
            data['message'] = ERROR_MESSAGE.parameterError
        else:
            current_balance = get_balance(id)
            if current_balance is not None:
                data = {
                    'success': True,
                    'data': current_balance
                }
            else:
                data['message'] = ERROR_MESSAGE.bankAccountNotFound
    return JsonResponse(data)
