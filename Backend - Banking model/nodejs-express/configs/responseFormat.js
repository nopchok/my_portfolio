module.exports = (req, res, next) => {
  res.success = (data = "", statusCode = 200) => {
    res.status(statusCode || 200).send(data);
  };

  res.error = ({ message, status = 500, code, errors }) => {
    let errorBody = { status: status, message: message };
    if (errors) errorBody.errors = errors;
    if (code) errorBody.code = code;
    res.status(status || 500).send({ error: errorBody });
  };

  next();
};
