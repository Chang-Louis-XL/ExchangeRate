import React, { useState, useEffect } from 'react';
import axios from 'axios';

function HomePage() {
  // 貨幣轉換相關狀態
  const [supportedCurrencies, setSupportedCurrencies] = useState([]);
  const [fromCurrency, setFromCurrency] = useState('');
  const [toCurrency, setToCurrency] = useState('');
  const [amount, setAmount] = useState('');
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);
  const [validated, setValidated] = useState(false);
  const [loading, setLoading] = useState(true);

  // 組件掛載時獲取支持的貨幣
  useEffect(() => {
    fetchSupportedCurrencies();
  }, []);

  // 從 API 獲取支持的貨幣
  const fetchSupportedCurrencies = async () => {
    try {
      setLoading(true);
      const response = await axios.get('/api/currencies');
      setSupportedCurrencies(response.data);
      setLoading(false);
    } catch (err) {
      setError('無法獲取支持的貨幣列表');
      setLoading(false);
    }
  };

  function handleSubmit(e) {
    e.preventDefault();

    // 表單驗證
    const form = e.currentTarget;
    if (!form.checkValidity()) {
      e.stopPropagation();
      setValidated(true);
      return;
    }

    // 隱藏之前的結果和錯誤
    setResult(null);
    setError(null);

    // 使用 axios 發送 API 請求
    axios.post('/api/currency/convert', {
      from_currency: fromCurrency,
      to_currency: toCurrency,
      amount: parseFloat(amount)
    })
    .then(response => {
      setResult(response.data);
    })
    .catch(err => {
      let errorMessage = '發生錯誤，請稍後再試。';
      if (err.response?.data?.error) {
        errorMessage = err.response.data.error;
      } else if (err.response?.data?.errors) {
        errorMessage = Object.values(err.response.data.errors).flat().join('\n');
      }
      setError(errorMessage);
    });
  }

  // 處理載入狀態
  if (loading) {
    return (
      <div className="container mt-5 text-center">
        <div className="spinner-border" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
        <p className="mt-2">正在載入貨幣資料...</p>
      </div>
    );
  }

  // 主要頁面內容
  return (
    <div className="container mt-5">
      {/* 貨幣轉換器 */}
      <div className="row justify-content-center">
        <div className="col-md-8">
          <div className="card shadow">
            <div className="card-header bg-primary text-white">
              <h3 className="mb-0">貨幣轉換器</h3>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit} className={`needs-validation ${validated ? 'was-validated' : ''}`} noValidate>
                <div className="mb-3 row">
                  <label htmlFor="fromCurrency" className="col-sm-3 col-form-label">從貨幣:</label>
                  <div className="col-sm-9">
                    <select
                      className="form-select"
                      id="fromCurrency"
                      value={fromCurrency}
                      onChange={(e) => setFromCurrency(e.target.value)}
                      required
                    >
                      <option value="">選擇貨幣</option>
                      {supportedCurrencies.map(currency => (
                        <option key={currency} value={currency}>{currency}</option>
                      ))}
                    </select>
                    <div className="invalid-feedback">請選擇來源貨幣</div>
                  </div>
                </div>

                <div className="mb-3 row">
                  <label htmlFor="toCurrency" className="col-sm-3 col-form-label">至貨幣:</label>
                  <div className="col-sm-9">
                    <select
                      className="form-select"
                      id="toCurrency"
                      value={toCurrency}
                      onChange={(e) => setToCurrency(e.target.value)}
                      required
                    >
                      <option value="">選擇貨幣</option>
                      {supportedCurrencies.map(currency => (
                        <option key={currency} value={currency}>{currency}</option>
                      ))}
                    </select>
                    <div className="invalid-feedback">請選擇目標貨幣</div>
                  </div>
                </div>

                <div className="mb-3 row">
                  <label htmlFor="amount" className="col-sm-3 col-form-label">金額:</label>
                  <div className="col-sm-9">
                    <input
                      type="number"
                      className="form-control"
                      id="amount"
                      value={amount}
                      onChange={(e) => setAmount(e.target.value)}
                      min="0.01"
                      step="0.01"
                      required
                    />
                    <div className="invalid-feedback">請輸入有效金額</div>
                  </div>
                </div>

                <div className="d-grid gap-2">
                  <button type="submit" className="btn btn-primary">轉換</button>
                </div>
              </form>

              {result && (
                <div className="mt-4 p-3 border rounded bg-light result-box">
                  <h4 className="mb-3">轉換結果</h4>
                  <div className="row">
                    <div className="col-md-6">
                      <p><strong>從貨幣:</strong> {result.from_currency}</p>
                      <p><strong>金額:</strong> {result.amount}</p>
                      <p><strong>使用匯率:</strong> {result.rate}</p>
                    </div>
                    <div className="col-md-6">
                      <p><strong>至貨幣:</strong> {result.to_currency}</p>
                      <p><strong>轉換後金額:</strong> {result.converted_amount}</p>
                      <p><strong>最新匯率時間:</strong> {new Date(result.last_updated).toLocaleString()}</p>
                    </div>
                  </div>
                </div>
              )}

              {error && (
                <div className="mt-4 p-3 border rounded bg-danger text-white result-box">
                  <p className="mb-0">{error}</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default HomePage;
