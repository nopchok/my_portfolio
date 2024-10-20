// const errMsg = require("../configs/errorMessage");
const err = require("../configs/errorMethods");

const db = require("../configs/databases");

const bcrypt = require('bcrypt');
const saltRounds = 10;

const jwt = require('jsonwebtoken');
const config = require("../configs/middlewares");

const methods = {
  register(param) {
    return new Promise(async (resolve, reject) => {
      const trx = await db.transaction();
      try {
        let { email, password } = param;
        password = await bcrypt.hash(password, saltRounds);

        const exist = await trx('users').where({ email: email });

        if (exist.length > 0) throw err.ErrorMessage('Email exist');

        const new_id = await trx
          .insert({
            email: email,
            password: password,
          })
          .returning("Id")
          .into("users");

        await trx.commit();
        resolve({ success: true, data: { new_id: new_id[0].Id } });
      } catch (error) {
        await trx.rollback();
        reject(error);
      }
    });
  },
  login(param) {
    return new Promise(async (resolve, reject) => {
      try {
        let { email, password } = param;
        const data = await db('users').where({ email: email });

        if (data.length == 0) throw err.ErrorMessage('Not found email');

        const isLogin = await bcrypt.compare(password, data[0].password);

        if (!isLogin) throw err.ErrorMessage('Incorrect password')

        const token = jwt.sign({ email: email }, config.secret, { expiresIn: config.jwt_expire });
        resolve({ success: true, token: token });
      } catch (error) {
        reject(error);
      }
    });
  },
};

module.exports = { ...methods };
