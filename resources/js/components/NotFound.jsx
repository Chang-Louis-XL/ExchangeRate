import React from 'react';
import { Link } from 'react-router-dom';

const NotFound = () => {
  return (
    <div className="container mt-5 text-center">
      <div className="alert alert-warning">
        <h2>404 - 頁面不存在</h2>
        <p>抱歉，您訪問的頁面不存在。</p>
        <Link to="/" className="btn btn-primary">返回首頁</Link>
      </div>
    </div>
  );
};

export default NotFound;
