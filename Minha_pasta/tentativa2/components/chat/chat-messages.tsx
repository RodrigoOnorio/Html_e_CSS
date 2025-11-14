"use client"

import { useState, useEffect, useRef } from "react"
import { MoreVertical } from "lucide-react"
import MessageMenu from "./message-menu"
import EditMessageBox from "./edit-message-box"

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

interface ChatMessagesProps {
  messages: Message[]
  currentUser: string
  onEditMessage: (messageId: number, newText: string) => void
  onDeleteMessageForMe: (messageId: number) => void
  onDeleteMessageForAll: (messageId: number) => void
}

export default function ChatMessages({
  messages,
  currentUser,
  onEditMessage,
  onDeleteMessageForMe,
  onDeleteMessageForAll,
}: ChatMessagesProps) {
  const [hoveredMessageId, setHoveredMessageId] = useState<number | null>(null)
  const [menuOpenId, setMenuOpenId] = useState<number | null>(null)
  const [editingId, setEditingId] = useState<number | null>(null)
  const menuRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        setMenuOpenId(null)
      }
    }

    if (menuOpenId !== null) {
      document.addEventListener("mousedown", handleClickOutside)
      return () => document.removeEventListener("mousedown", handleClickOutside)
    }
  }, [menuOpenId])

  const handleSaveEdit = (messageId: number, newText: string) => {
    onEditMessage(messageId, newText)
    setEditingId(null)
  }

  return (
    <div className="flex-1 overflow-y-auto p-4 space-y-3 bg-chat-background dark:bg-slate-900">
      {messages.length === 0 ? (
        <div className="flex items-center justify-center h-full text-muted-foreground">
          <div className="text-center">
            <img src="/logo-flametalk.png" alt="FlameTalk" className="w-16 h-16 mx-auto mb-3" />
            <p>Inicie uma conversa enviando uma mensagem</p>
          </div>
        </div>
      ) : (
        messages.map((message) => {
          if (message.deletedForMe) {
            return null
          }

          return (
            <div
              key={message.id}
              className={`flex ${message.user === currentUser ? "justify-end" : "justify-start"}`}
              onMouseEnter={() => message.user === currentUser && setHoveredMessageId(message.id)}
              onMouseLeave={() => setHoveredMessageId(null)}
            >
              <div className="relative group">
                {editingId === message.id && (
                  <EditMessageBox
                    initialText={message.originalText || message.text}
                    onSave={(newText) => handleSaveEdit(message.id, newText)}
                    onCancel={() => setEditingId(null)}
                  />
                )}

                <div
                  className={`max-w-xs px-4 py-2 rounded-2xl relative ${
                    message.user === currentUser
                      ? "bg-blue-600 text-white rounded-br-none"
                      : "bg-white dark:bg-slate-700 dark:text-white text-gray-900 border dark:border-slate-600 rounded-bl-none"
                  }`}
                >
                  {message.user !== currentUser && (
                    <p className="text-xs font-semibold opacity-70 mb-1">{message.user}</p>
                  )}

                  {message.deleted ? (
                    <div className="flex items-center gap-2 text-sm opacity-60">
                      <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" strokeWidth="2" />
                        <line x1="5" y1="5" x2="19" y2="19" stroke="currentColor" strokeWidth="2" />
                      </svg>
                      Mensagem apagada
                    </div>
                  ) : (
                    <>
                      <p className="text-sm break-words">{message.text}</p>
                      {message.edited && <span className="text-xs opacity-70 ml-1 italic">Editado</span>}
                    </>
                  )}

                  <span className="text-xs opacity-70 mt-1 block text-right">{formatTime(message.timestamp)}</span>
                </div>

                {message.user === currentUser && (hoveredMessageId === message.id || menuOpenId === message.id) && (
                  <button
                    onClick={() => setMenuOpenId(menuOpenId === message.id ? null : message.id)}
                    className="absolute top-1 -left-8 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white p-1 rounded"
                  >
                    <MoreVertical className="w-4 h-4" />
                  </button>
                )}

                {menuOpenId === message.id && (
                  <div ref={menuRef}>
                    <MessageMenu
                      messageId={message.id}
                      sentTime={message.timestamp}
                      onEdit={() => setEditingId(message.id)}
                      onDeleteForMe={() => {
                        onDeleteMessageForMe(message.id)
                        setMenuOpenId(null)
                      }}
                      onDeleteForAll={() => {
                        onDeleteMessageForAll(message.id)
                        setMenuOpenId(null)
                      }}
                      onClose={() => setMenuOpenId(null)}
                    />
                  </div>
                )}
              </div>
            </div>
          )
        })
      )}
    </div>
  )
}

function formatTime(date: Date) {
  return new Date(date).toLocaleTimeString("pt-BR", { hour: "2-digit", minute: "2-digit" })
}

function formatFileSize(bytes: number): string {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
