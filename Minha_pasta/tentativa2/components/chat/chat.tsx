"use client"

import type React from "react"
import { useState, useEffect, useRef } from "react"
import { Input } from "@/components/ui/input"
import ChatHeader from "./chat-header"
import ChatMessages from "./chat-messages"
import EmojiPickerButton from "./emoji-picker-button"

interface Message {
  id: number
  user: string
  text: string
  timestamp: Date
  type: "text" | "image" | "video" | "audio" | "file"
  fileUrl?: string
  fileName?: string
  fileMime?: string
  fileSize?: number
  edited?: boolean
  editedAt?: Date
  deleted?: boolean
  deletedForMe?: boolean
  originalText?: string
}

interface ChatProps {
  user: string
  onLogout: () => void
}

export default function Chat({ user, onLogout }: ChatProps) {
  const [messages, setMessages] = useState<Message[]>([])
  const [inputValue, setInputValue] = useState("")
  const [isDark, setIsDark] = useState(false)
  const [loading, setLoading] = useState(false)
  const messagesEndRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const loadMessages = async () => {
      try {
        const response = await fetch("http://localhost/tentativa2/api/messages/get.php")
        const data = await response.json()
        if (data.success) {
          setMessages(
            data.messages.map((msg: any) => ({
              id: msg.id,
              user: msg.usuario,
              text: msg.conteudo,
              timestamp: new Date(msg.data_criacao),
              type: msg.tipo || 'text',
              fileUrl: msg.arquivo_url,
              fileName: msg.arquivo_nome,
              fileMime: msg.arquivo_mime,
              fileSize: msg.arquivo_tamanho,
              edited: msg.editada,
              editedAt: msg.data_edicao ? new Date(msg.data_edicao) : undefined,
              deleted: msg.deletada || msg.deletada_para_todos,
              deletedForMe: false, // Será tratado posteriormente
              originalText: msg.originalText,
            })),
          )
        }
      } catch (err) {
        console.error("[v0] Error loading messages:", err)
      }
    }

    loadMessages()
    const darkMode = localStorage.getItem("darkMode") === "true"
    setIsDark(darkMode)

    const interval = setInterval(loadMessages, 2000)
    return () => clearInterval(interval)
  }, [])

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" })
  }

  useEffect(() => {
    scrollToBottom()
  }, [messages])

  const handleSendMessage = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!inputValue.trim() || loading) return

    setLoading(true)
    try {
      const response = await fetch("http://localhost/tentativa2/api/messages/send.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          userId: localStorage.getItem("userId"),
          text: inputValue,
          type: "text",
        }),
      })

      const data = await response.json()
      if (data.success) {
        setInputValue("")
        const messagesResponse = await fetch("http://localhost/tentativa2/api/messages/get.php")
        const messagesData = await messagesResponse.json()
        if (messagesData.success) {
          setMessages(
            messagesData.messages.map((msg: any) => ({
              id: msg.id,
              user: msg.usuario,
              text: msg.conteudo,
              timestamp: new Date(msg.data_criacao),
              type: msg.tipo || 'text',
              fileUrl: msg.arquivo_url,
              fileName: msg.arquivo_nome,
              fileMime: msg.arquivo_mime,
              fileSize: msg.arquivo_tamanho,
              edited: msg.editada,
              editedAt: msg.data_edicao ? new Date(msg.data_edicao) : undefined,
              deleted: msg.deletada || msg.deletada_para_todos,
              deletedForMe: false,
              originalText: msg.originalText,
            })),
          )
        }
      }
    } catch (err) {
      console.error("[v0] Error sending message:", err)
    } finally {
      setLoading(false)
    }
  }

  const handleEditMessage = async (messageId: number, newText: string) => {
    try {
      const response = await fetch("http://localhost/tentativa2/api/messages/edit.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          messageId,
          newText,
        }),
      })

      const data = await response.json()
      if (data.success) {
        setMessages(
          messages.map((msg) =>
            msg.id === messageId
              ? {
                  ...msg,
                  text: newText,
                  edited: true,
                  editedAt: new Date(),
                  originalText: msg.originalText || msg.text,
                }
              : msg,
          ),
        )
      }
    } catch (err) {
      console.error("[v0] Error editing message:", err)
    }
  }

  const handleDeleteMessageForMe = async (messageId: number) => {
    try {
      const response = await fetch("http://localhost/tentativa2/api/messages/delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          messageId,
          deleteType: "for-me",
          userId: localStorage.getItem("userId"),
        }),
      })

      const data = await response.json()
      if (data.success) {
        setMessages(messages.map((msg) => (msg.id === messageId ? { ...msg, deletedForMe: true } : msg)))
      }
    } catch (err) {
      console.error("[v0] Error deleting message for me:", err)
    }
  }

  const handleDeleteMessageForAll = async (messageId: number) => {
    try {
      const response = await fetch("http://localhost/tentativa2/api/messages/delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          messageId,
          deleteType: "for-all",
        }),
      })

      const data = await response.json()
      if (data.success) {
        setMessages(messages.map((msg) => (msg.id === messageId ? { ...msg, deleted: true, text: "" } : msg)))
      }
    } catch (err) {
      console.error("[v0] Error deleting message for all:", err)
    }
  }

  const handleEmojiSelect = (emoji: string) => {
    setInputValue((prev) => prev + emoji)
  }

  const handleFileSelect = async (fileData: any) => {
    console.log("[v0] File selected:", fileData.fileName)
    
    setLoading(true)
    try {
      // Enviar mensagem com arquivo para o backend
      const response = await fetch("http://localhost/tentativa2/api/messages/send.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          userId: localStorage.getItem("userId"),
          text: `[Arquivo: ${fileData.fileName}]`,
          type: fileData.type || 'file',
          fileUrl: fileData.fileUrl,
          fileName: fileData.fileName,
          fileMime: fileData.mimeType,
          fileSize: fileData.fileSize
        }),
      })

      const data = await response.json()
      if (data.success) {
        // Recarregar mensagens
        const messagesResponse = await fetch("http://localhost/tentativa2/api/messages/get.php")
        const messagesData = await messagesResponse.json()
        if (messagesData.success) {
          setMessages(
            messagesData.messages.map((msg: any) => ({
              id: msg.id,
              user: msg.usuario,
              text: msg.conteudo,
              timestamp: new Date(msg.data_criacao),
              type: msg.tipo || 'text',
              fileUrl: msg.arquivo_url,
              fileName: msg.arquivo_nome,
              fileMime: msg.arquivo_mime,
              fileSize: msg.arquivo_tamanho,
              edited: msg.editada,
              editedAt: msg.data_edicao ? new Date(msg.data_edicao) : undefined,
              deleted: msg.deletada || msg.deletada_para_todos,
              deletedForMe: false,
              originalText: msg.originalText,
            })),
          )
        }
      }
    } catch (err) {
      console.error("[v0] Error sending file message:", err)
    } finally {
      setLoading(false)
    }
  }

  const toggleDarkMode = () => {
    setIsDark(!isDark)
    localStorage.setItem("darkMode", String(!isDark))
  }

  return (
    <div className={isDark ? "dark" : ""}>
      <div className="min-h-screen bg-background text-foreground transition-colors duration-300">
        <ChatHeader user={user} onLogout={onLogout} isDark={isDark} onToggleDark={toggleDarkMode} />

        <div className="max-w-2xl mx-auto h-screen flex flex-col bg-background">
          <ChatMessages
            messages={messages}
            currentUser={user}
            onEditMessage={handleEditMessage}
            onDeleteMessageForMe={handleDeleteMessageForMe}
            onDeleteMessageForAll={handleDeleteMessageForAll}
          />
          <div ref={messagesEndRef} />

          <div className="border-t bg-card p-4">
            <form onSubmit={handleSendMessage} className="flex gap-2 items-end">
              <EmojiPickerButton onEmojiSelect={handleEmojiSelect} onFileSelect={handleFileSelect} isDark={isDark} />
              <Input
                value={inputValue}
                onChange={(e) => setInputValue(e.target.value)}
                placeholder="Digite uma mensagem..."
                className="flex-1 rounded-2xl"
                disabled={loading}
              />
              <button
                type="submit"
                className="rounded-full w-10 h-10 p-0 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white transition-colors disabled:opacity-50"
                aria-label="Enviar mensagem"
                disabled={loading}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
                </svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}
