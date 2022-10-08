from bankingapi.views.common import *




@csrf_exempt
def newBankAccount(request):
    data = {
        'success': False
    }
    is_ok = request.method == 'POST'
    
    if is_ok:
        post_data = json.loads(request.body)
        customer_name = post_data.get('name')
        initial_deposit = post_data.get('initial_deposit')
        
        is_ok = (customer_name is not None) and (initial_deposit is not None)
        if not is_ok: data['message'] = ERROR_MESSAGE.parameterError
        
    if is_ok:
        try:
            initial_deposit = round(Decimal(initial_deposit),2)
            if not Customer.objects.filter(Name=customer_name).exists():
                newCustomer = Customer.objects.create(Name=customer_name)
            
            newCustomer = Customer.objects.get(Name=customer_name)

            newBankAccount = BankAccount.objects.create(CustomerId=newCustomer, CurrentBalance=initial_deposit)
            
            TransactionHistory.objects.create(Type='initial_deposit', BankAccountId=newBankAccount, Amount=initial_deposit)
            
            data = {
                'success': True,
                'data': {
                    'BankAccountId': newBankAccount.Id
                }
            }
        except:
            print(sys.exc_info())
            data['message'] = ERROR_MESSAGE.somethingWentWrong

    return JsonResponse(data)



@csrf_exempt
def transferAmount(request):
    data = {
        'success': False
    }
    is_ok = request.method == 'POST'

    if is_ok:
        post_data = json.loads(request.body)
        from_bank_account_id = post_data.get('from_bank_account_id')
        to_bank_account_id = post_data.get('to_bank_account_id')
        amount = post_data.get('amount')
        
        is_ok = (from_bank_account_id is not None) and (to_bank_account_id is not None) and (amount is not None)
        if not is_ok: data['message'] = ERROR_MESSAGE.parameterError

    if is_ok:
        is_ok = Decimal(amount) > 0
        if not is_ok: data['message'] = ERROR_MESSAGE.amountZero

    if is_ok:
        is_ok = from_bank_account_id != to_bank_account_id
        if not is_ok: data['message'] = ERROR_MESSAGE.sameAccount
    
    if is_ok:
        is_ok = BankAccount.objects.filter(Id=from_bank_account_id).exists() and BankAccount.objects.filter(Id=to_bank_account_id).exists()
        if not is_ok: data['message'] = ERROR_MESSAGE.bankAccountNotFound

    if is_ok:
        amount = round(Decimal(amount),2)
        from_bank_account_balance = get_balance(from_bank_account_id)
        is_ok = from_bank_account_balance >= amount
        if not is_ok: data['message'] = ERROR_MESSAGE.balanceNotEnough
        
    if is_ok:
        try:
            from_bank_account = BankAccount.objects.get(Id=from_bank_account_id)
            to_bank_account = BankAccount.objects.get(Id=to_bank_account_id)


            Transaction = TransactionHistory.objects.create(Type='withdraw', BankAccountId=from_bank_account, RelateBankAccountId=to_bank_account, Amount=-amount)
            from_bank_account.CurrentBalance -= amount
            from_bank_account.save()

            
            TransactionHistory.objects.create(Type='deposit', BankAccountId=to_bank_account, RelateBankAccountId=from_bank_account, Amount=amount)
            to_bank_account.CurrentBalance += amount
            to_bank_account.save()


            data = {
                'success': True,
                'data': {
                    'TransactionId': Transaction.Id
                }
            }
        except:
            print(sys.exc_info())
            data['message'] = ERROR_MESSAGE.somethingWentWrong

    return JsonResponse(data)
