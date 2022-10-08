from bankingapi.views.common import *
from django.views.generic import View




class MyClassView(View):

    def get(self, request, action):
        self.request_action(MethodGET(), request, action)
        return JsonResponse(self.res)
        
    def post(self, request, action):
        self.request_action(MethodPOST(), request, action)
        return JsonResponse(self.res)
        
    def put(self, request, action):
        self.request_action(MethodPUT(), request, action)
        return JsonResponse(self.res)
        
    def delete(self, request, action):
        self.request_action(MethodDELETE(), request, action)
        return JsonResponse(self.res)




    def __init__(self):
        self.res = {
            'success': False
        }

    def request_action(self, cls_method, req, act):
        self.cls_method = cls_method
        self.request = req
        self.action = act
        self.query_data = {}
        self.request_data = {}
        self.act = None

        is_ok = True
        is_ok = is_ok if not is_ok else self.validate_header()
        is_ok = is_ok if not is_ok else self.validate_request()
        is_ok = is_ok if not is_ok else self.validate_action()

        if is_ok:
            self.act( {'query_data': self.query_data, 'request_data': self.request_data} )
            self.res = self.cls_method.response

    def validate_header(self):
        result = True
        if not result:
            self.res['message'] = ERROR_MESSAGE.headerError
        return result

    def validate_request(self):
        result = False
        try:
            if self.request.method == 'GET':
                self.query_data = self.request.GET.dict()
                result = True
            else:
                if str(self.request.body) != '':
                    self.request_data = json.loads(self.request.body)
                    result = True
                else:
                    self.res['message'] = ERROR_MESSAGE.requestDataErro
        except:
            self.res['message'] = ERROR_MESSAGE.somethingWentWrong
            print(sys.exc_info())
            
        return result

    def validate_action(self):
        result = False
        try:
            self.act = getattr(self.cls_method, self.action, None)
            if self.act is None:
                self.res['message'] = ERROR_MESSAGE.actionNotFound
            else:
                result = True
        except:
            self.res['message'] = ERROR_MESSAGE.somethingWentWrong
            print(sys.exc_info())
        return result








# CUSTOM ANY ACTION (METHOD DELETE)
class MethodDELETE():
    response = {
        'success': False
    }







# CUSTOM ANY ACTION (METHOD PUT)
class MethodPUT():
    response = {
        'success': False
    }






# CUSTOM ANY ACTION (METHOD GET)
class MethodGET():
    response = {
        'success': False
    }

    def get_test(self, rq_data):
        self.response = {
            'success': True,
            'query_data': rq_data['query_data']
        }




# CUSTOM ANY ACTION (METHOD POST)
class MethodPOST():
    response = {
        'success': False
    }

    def get_current_balance(self, rq_data):
        id = rq_data['request_data'].get('bank_account_id')
        if id is None:
            self.response['message'] = ERROR_MESSAGE.parameterError
        else:
            current_balance = get_balance(id)
            if current_balance is not None:
                self.response = {
                    'success': True,
                    'data': current_balance
                }
            else:
                self.response['message'] = ERROR_MESSAGE.bankAccountNotFound
                