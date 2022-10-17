const config = require("../configs/middlewares");
const jwt = require('jsonwebtoken');

const { ErrorInvalidParameter } = require("../configs/errorMethods");
const { header, validationResult } = require("express-validator");

const methods = {
    validate_auth: [
        header('authorization').exists()
    ],
    isAuthorized: (req, res, next) => {

        const errors = validationResult(req);
        if (!errors.isEmpty()) throw ErrorInvalidParameter(errors.errors);

        if (!req.headers.authorization) throw new Error('Authorization is required')

        const token = req.headers.authorization.split(' ')[1];

        const decode = jwt.decode(token, config.secret);

        if (!decode) throw new Error('Invalid token')

        if (decode.exp < +new Date() / 1000) throw new Error('Token is expired')


        next();
    },
};



module.exports = { ...methods };