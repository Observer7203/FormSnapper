import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";

function ResponseView() {
  const { id, responseId } = useParams();
  const [response, setResponse] = useState(null);
  const [form, setForm] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [scores, setScores] = useState({});
  const [userRole, setUserRole] = useState("");
  const [totalScore, setTotalScore] = useState(0);
  const [maxScore, setMaxScore] = useState(0);
  const [isScorable, setIsScorable] = useState(false); // ✅ Добавили `isScorable`

  useEffect(() => {
    axios.get("/api/user-role")
      .then(response => {
        setUserRole(response.data.role.includes("ROLE_ADMIN") ? "admin" : "user");
      })
    .catch(error => console.error("Ошибка получения роли:", error));


    axios.get(`/api/forms/${id}`)
      .then((res) => {
        setForm(res.data);
        setIsScorable(res.data.isScorable || false); // ✅ Устанавливаем `isScorable`
      })
      .catch((error) => console.error("Ошибка загрузки формы:", error));

    if (responseId) {
      axios.get(`/api/forms/${id}/responses/${responseId}`)
        .then((res) => {
          setResponse(res.data);
          setScores(res.data.scores || {});
          setLoading(false);
        })
        .catch((error) => {
          console.error("Ошибка загрузки ответа:", error);
          setError("Ошибка загрузки данных");
          setLoading(false);
        });
    } else {
      axios.get(`/api/forms/${id}/my-response`)
        .then((res) => {
          setResponse(res.data);
          setLoading(false);
        })
        .catch(() => {
          setError("Вы еще не заполняли эту форму.");
          setLoading(false);
        });
    }
  }, [id, responseId]);

  useEffect(() => {
    if (form && response) {
      let total = 0;
      let max = 0;
      form.questions.forEach((q) => {
        if (q.isScorable && scores[q.id] !== undefined) {
          total += parseFloat(scores[q.id]) || 0;
          max += q.maxScore || 1;
        }
      });
      setTotalScore(total);
      setMaxScore(max);
    }
  }, [scores, form, response]);

  const handleScoreChange = (questionId, value) => {
    setScores({ ...scores, [questionId]: parseFloat(value) || 0 });
  };

  const handleScoreSubmit = async () => {
    try {
      await axios.post(`/api/forms/${id}/responses/${responseId}/score`, { scores });
      alert("Оценки сохранены!");
      window.location.reload();
    } catch (error) {
      console.error("Ошибка при выставлении оценки:", error);
      alert("Не удалось сохранить оценки.");
    }
  };

  if (loading) return <p>Загрузка...</p>;
  if (error) return <p className="text-danger">{error}</p>;

  return (
    <div className="container mt-4">
      <h2 className="text-center">
        {responseId ? `Ответ пользователя: ${response.user}` : "Ваши ответы"}
      </h2>

      <ul className="list-group">
        {form && response ? (
          form.questions.map((question, index) => (
            <li key={question.id} className="list-group-item">
              <strong>{index + 1}. {question.text}</strong>
              <div className="mt-2">
                {question.type === "file_upload" ? (
                  response.answers[question.id] ? (
                    <a href={response.answers[question.id]} target="_blank" rel="noopener noreferrer">
                      Скачать файл
                    </a>
                  ) : "Нет загруженного файла"
                ) : Array.isArray(response.answers[question.id]) ? (
                  response.answers[question.id].join(", ")
                ) : (
                  response.answers[question.id] || "Нет ответа"
                )}
              </div>

              {/* Оценка за вопрос */}
              {question.isScorable && responseId && userRole === "admin" && (
                <div className="mt-2">
                  <label className="form-label">Оценка за этот ответ:</label>
                  <input
                    type="number"
                    className="form-control"
                    value={scores[question.id] || ""}
                    onChange={(e) => handleScoreChange(question.id, e.target.value)}
                    min="0"
                    max={question.maxScore || 1}
                  />
                </div>
              )}
            </li>
          ))
        ) : (
          <p>Нет данных</p>
        )}
      </ul>

      {/* Итоговый балл */}
      {responseId && userRole === "admin" && isScorable && (
        <div className="alert alert-primary mt-3">
          Итоговый балл: <strong>{totalScore} / {maxScore}</strong>
        </div>
      )}

      {/* Кнопка для сохранения оценок */}
      {responseId && userRole === "admin" && isScorable && (
        <button className="btn btn-success mt-3" onClick={handleScoreSubmit}>
          Сохранить оценки
        </button>
      )}

      {/* Итоговый балл для пользователя */}
      {!responseId && isScorable && (
        <div className="alert alert-info mt-3">
          Итоговый балл: <strong>{response.score} / {maxScore}</strong>
        </div>
      )}
    </div>
  );
}

export default ResponseView;
