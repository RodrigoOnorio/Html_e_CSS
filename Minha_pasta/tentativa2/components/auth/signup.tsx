"use client"

import type React from "react"
import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { ArrowLeft, Eye, EyeOff } from "lucide-react"
import Image from "next/image"

interface SignUpProps {
  onSignUp: (username: string) => void
  onToggleLogin: () => void
}

export default function SignUp({ onSignUp, onToggleLogin }: SignUpProps) {
  const [username, setUsername] = useState("")
  const [password, setPassword] = useState("")
  const [confirmPassword, setConfirmPassword] = useState("")
  const [error, setError] = useState("")
  const [success, setSuccess] = useState("")
  const [loading, setLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setError("")
    setSuccess("")
    setLoading(true)

    if (!username || !password || !confirmPassword) {
      setError("Por favor, preencha todos os campos")
      setLoading(false)
      return
    }

    if (password !== confirmPassword) {
      setError("As senhas não coincidem")
      setLoading(false)
      return
    }

    if (password.length < 6) {
      setError("A senha deve ter no mínimo 6 caracteres")
      setLoading(false)
      return
    }

    try {
      const response = await fetch("http://localhost/tentativa2/signup.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      })

      const data = await response.json()

      if (data.success) {
        setSuccess("Cadastro realizado com sucesso!")
        setTimeout(() => {
          localStorage.setItem("userId", data.userId)
          localStorage.setItem("username", username)
          onSignUp(username)
        }, 1500)
      } else {
        setError(data.message || "Erro ao criar conta")
      }
    } catch (err) {
      setError("Erro ao conectar com o servidor")
      console.error("[v0] Signup error:", err)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-300 to-blue-200 p-4">
      <Card className="w-full max-w-md shadow-xl">
        <CardHeader className="space-y-2 text-center">
          <Button variant="ghost" size="sm" onClick={onToggleLogin} className="w-fit -ml-2 mb-2" disabled={loading}>
            <ArrowLeft className="w-4 h-4 mr-2" />
            Voltar
          </Button>
          <div className="w-16 h-16 mx-auto mb-2">
            <Image
              src="/logo-flametalk.png"
              alt="FlameTalk Logo"
              width={64}
              height={64}
              priority
              className="w-full h-full object-contain"
            />
          </div>
          <CardTitle className="text-3xl">Criar Conta</CardTitle>
          <CardDescription>Preencha os dados para se cadastrar</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <label className="text-sm font-medium">Usuário</label>
              <Input
                type="text"
                placeholder="Escolha um usuário"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                className="rounded-xl"
                disabled={loading}
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium">Senha</label>
              <div className="relative">
                <Input
                  type={showPassword ? "text" : "password"}
                  placeholder="Crie uma senha (mín. 6 caracteres)"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="rounded-xl pr-10"
                  disabled={loading}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  disabled={loading}
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>
            <div className="space-y-2">
              <label className="text-sm font-medium">Confirmar Senha</label>
              <div className="relative">
                <Input
                  type={showConfirmPassword ? "text" : "password"}
                  placeholder="Confirme sua senha"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  className="rounded-xl pr-10"
                  disabled={loading}
                />
                <button
                  type="button"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                  className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  disabled={loading}
                >
                  {showConfirmPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            {error && <div className="text-sm text-red-600 bg-red-50 p-3 rounded-lg">{error}</div>}
            {success && <div className="text-sm text-green-600 bg-green-50 p-3 rounded-lg">{success}</div>}

            <Button type="submit" className="w-full rounded-xl h-10" disabled={loading}>
              {loading ? "Cadastrando..." : "Cadastrar"}
            </Button>
          </form>

          <div className="mt-6 pt-6 border-t text-center">
            <p className="text-sm text-muted-foreground">
              Já tem uma conta?{" "}
              <button onClick={onToggleLogin} className="text-primary font-semibold hover:underline" disabled={loading}>
                Faça login
              </button>
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
