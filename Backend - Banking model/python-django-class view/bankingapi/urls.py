from django.urls import path, re_path
from bankingapi.views import *

from django.views.decorators.csrf import csrf_exempt

urlpatterns=[
    path('api/<action>', csrf_exempt(myclass.MyClassView.as_view())),
]