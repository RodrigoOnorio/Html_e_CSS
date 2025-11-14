import mysql from "mysql2/promise"

const dbConfig = {
  host: "localhost",
  user: "root",
  password: "",
  database: "tentativa2",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
}

let pool: mysql.Pool | null = null

export async function getPool() {
  if (!pool) {
    pool = mysql.createPool(dbConfig)
  }
  return pool
}

export async function query(sql: string, values?: any[]) {
  const pool = await getPool()
  const connection = await pool.getConnection()
  try {
    const [results] = await connection.execute(sql, values)
    return results
  } finally {
    connection.release()
  }
}
