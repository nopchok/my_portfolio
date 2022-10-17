const model = require("../models/user.model");

const { ErrorInvalidParameter } = require("../configs/errorMethods");
const { body, validationResult } = require("express-validator");

const methods = {
  validate_register: [
    body('email').isEmail(),
    body('password').exists(),
    body('c_password', "Passwords don't match").exists()
      .custom((value, { req }) => value === req.body.password)
  ],
  validate_login: [
    body('email').isEmail(),
    body('password').exists(),
  ],
  async register(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) throw ErrorInvalidParameter(errors.errors);

      const result = await model.register(req.body);
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
  async login(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) throw ErrorInvalidParameter(errors.errors);

      const result = await model.login(req.body);
      res.success(result);
    } catch (error) {
      res.error(error);
    }
  },
};

module.exports = { ...methods };
