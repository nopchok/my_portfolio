const model = require("../models/get.model");

const { ErrorInvalidParameter } = require("../configs/errorMethods");
const { body, validationResult } = require("express-validator");

const methods = {
  validateCurrentbalance(){
    return [ 
      body('id').exists(),
    ] 
  },
  validateTransactionhistory(){
    return [ 
      body('id').exists(),
    ] 
  },
  async currentbalance(req, res) {
    try {
      const errors = validationResult(req);
      if ( !errors.isEmpty() ) throw ErrorInvalidParameter(errors.errors);

      const result = await model.CurrentBalance(req.body);
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
  async transactionhistory(req, res) {
    try {
      const errors = validationResult(req);
      if ( !errors.isEmpty() ) throw ErrorInvalidParameter(errors.errors);
      
      const result = await model.TransactionHistory(req.body);
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
};

module.exports = { ...methods };
