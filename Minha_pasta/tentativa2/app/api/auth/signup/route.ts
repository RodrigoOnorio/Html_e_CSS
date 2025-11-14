import { type NextRequest, NextResponse } from "next/server"
import { query } from "@/app/api/db"

export async function POST(req: NextRequest) {
  try {
    const { username, password, confirmPassword } = await req.json()

    // Validações
    if (!username || !password || !confirmPassword) {
      return NextResponse.json({ error: "Todos os campos são obrigatórios" }, { status: 400 })
    }

    if (password !== confirmPassword) {
      return NextResponse.json({ error: "As senhas não coincidem" }, { status: 400 })
    }

    if (password.length < 6) {
      return NextResponse.json({ error: "A senha deve ter pelo menos 6 caracteres" }, { status: 400 })
    }

    const existingUser: any = await query("SELECT id FROM users WHERE username = ?", [username])

    if (existingUser.length > 0) {
      return NextResponse.json({ error: "Este usuário já está cadastrado" }, { status: 400 })
    }

    await query("INSERT INTO users (username, password) VALUES (?, ?)", [username, password])

    return NextResponse.json({
      message: "Cadastro realizado com sucesso",
      username,
    })
  } catch (error) {
    console.error("Signup error:", error)
    return NextResponse.json({ error: "Erro ao fazer cadastro" }, { status: 500 })
  }
}
