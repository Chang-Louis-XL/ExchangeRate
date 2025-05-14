import React from 'react';
import ReactDOM from 'react-dom/client';
import axios from 'axios';

// 設置axios預設值
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = '/api';

// 匯入主要的App元件(這裡可能需要根據您的實際情況修改)
// import App from './components/App';

const App = () => {
    return (
        <div className="container mt-5">
            <h1>匯率查詢系統</h1>
            <p>歡迎使用匯率查詢系統。這是一個使用Laravel和React構建的應用程序。</p>
            <p>請在components目錄中添加您的組件並在此處引用它們。</p>
        </div>
    );
};

// 渲染React應用
if (document.getElementById('app')) {
    const root = ReactDOM.createRoot(document.getElementById('app'));
    root.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
}
