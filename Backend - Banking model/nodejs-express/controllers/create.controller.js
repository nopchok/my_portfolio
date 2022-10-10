const model = require("../models/create.model");

const { ErrorInvalidParameter } = require("../configs/errorMethods");
const { body, validationResult } = require("express-validator");

const methods = {
  validate_newaccount: [ 
    body('name').exists(),
    body('initial_deposit').exists(),
  ],
  validate_transferamount: [ 
    body('from').exists(),
    body('to').exists(),
    body('amount').exists(),
    body('transaction_type').exists(),
    body('relate_transaction_type').exists(),
  ],
  async newaccount(req, res) {
    try {
      const errors = validationResult(req);
      if ( !errors.isEmpty() ) throw ErrorInvalidParameter(errors.errors);

      const result = await model.newaccount(req.body);
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
  async transferamount(req, res) {
    try {
      const errors = validationResult(req);
      if ( !errors.isEmpty() ) throw ErrorInvalidParameter(errors.errors);
      
      const result = await model.transferamount(req.body);
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
};

module.exports = { ...methods };
